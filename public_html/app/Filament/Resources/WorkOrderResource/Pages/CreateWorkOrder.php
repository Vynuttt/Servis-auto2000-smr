<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\WorkOrderStage;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/** @property \App\Models\WorkOrder $record */
class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;

    /**
     * Mount - Pre-fill form data from booking_id query parameter
     */
    public function mount(): void
    {
        parent::mount();

        // Jika ada booking_id dari URL
        if (request()->has('booking_id')) {
            $bookingId = request()->get('booking_id');
            $booking = Booking::with(['customer', 'vehicle'])->find($bookingId);
            
            if ($booking) {
                // Set form data
                $this->form->fill([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'planned_start' => $this->getPlannedStartFromBooking($booking),
                    'sla_minutes' => $booking->sla_minutes ?? 120,
                    'planned_finish' => $this->getPlannedFinishFromBooking($booking),
                    'notes' => $booking->complaint_note ?? $booking->notes ?? null,
                    'status' => WorkOrder::S_PLANNED,
                    'priority' => 'Regular',
                ]);
            }
        }
    }

    /**
     * Helper: Get planned start from booking
     */
    protected function getPlannedStartFromBooking(Booking $booking): ?Carbon
    {
        if ($booking->scheduled_at) {
            return Carbon::parse($booking->scheduled_at);
        }
        
        if ($booking->booking_date && $booking->booking_time) {
            return Carbon::parse("{$booking->booking_date} {$booking->booking_time}");
        }
        
        if ($booking->booking_date) {
            return Carbon::parse($booking->booking_date)->setTime(8, 0);
        }
        
        return null;
    }

    /**
     * Helper: Get planned finish from booking
     */
    protected function getPlannedFinishFromBooking(Booking $booking): ?Carbon
    {
        $start = $this->getPlannedStartFromBooking($booking);
        $sla = $booking->sla_minutes ?? 120;
        
        if ($start) {
            return $start->copy()->addMinutes($sla);
        }
        
        return null;
    }

    /**
     * --- Mutasi sebelum create ---
     * - Generate nomor WO unik
     * - Isi otomatis customer_id dari booking
     * - Hitung planned_finish dari planned_start + SLA
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['status']   ??= 'Planned';
        $data['priority'] ??= 'Regular';

        // Ambil customer_id dari booking bila belum terisi
        if (empty($data['customer_id']) && !empty($data['booking_id'])) {
            $booking = Booking::find($data['booking_id']);
            if ($booking && $booking->customer_id) {
                $data['customer_id'] = $booking->customer_id;
            }
        }

        // Pastikan planned_finish terhitung
        if (!empty($data['planned_start'])) {
            $sla = (int)($data['sla_minutes'] ?? 120);
            $data['planned_finish'] = Carbon::parse($data['planned_start'])
                ->addMinutes($sla);
        }

        // CRITICAL: Generate WO Number SEBELUM insert
        $data['wo_number'] = $this->generateWONumber();

        return $data;
    }

    /**
     * Generate unique WO number
     */
    protected function generateWONumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd') . '-';
        
        $lastWO = WorkOrder::whereDate('created_at', now()->toDateString())
            ->where('wo_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('wo_number');

        $seq = 1;
        if ($lastWO && preg_match('/-(\d{4})$/', $lastWO, $matches)) {
            $seq = (int)$matches[1] + 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * --- Handle record creation ---
     * Melakukan retry bila nomor WO duplikat (race condition)
     */
    protected function handleRecordCreation(array $data): Model
    {
        $maxRetries = 3;
        
        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            try {
                // Create WorkOrder
                $workOrder = static::getModel()::create($data);
                
                // Update Booking status jika ada
                if (!empty($data['booking_id'])) {
                    $booking = Booking::find($data['booking_id']);
                    if ($booking) {
                        $booking->update(['status' => 'Checked-In']);
                    }
                }
                
                return $workOrder;
                
            } catch (QueryException $e) {
                // Handle duplicate key error (MySQL error 1062)
                $errorCode = $e->errorInfo[1] ?? null;
                
                if ($errorCode === 1062) {
                    // Regenerate WO number untuk retry
                    $data['wo_number'] = $this->generateWONumber();
                    
                    // Sleep sebentar untuk menghindari collision
                    usleep(100000); // 0.1 detik
                    continue;
                }
                
                // Throw error lain
                throw $e;
            }
        }

        // Jika retry gagal semua
        throw ValidationException::withMessages([
            'wo_number' => 'Gagal membuat nomor WO unik setelah ' . $maxRetries . ' percobaan. Silakan coba lagi.',
        ]);
    }

    /**
     * --- Setelah create ---
     * Buat log pertama & set stage awal
     */
    protected function afterCreate(): void
    {
        /** @var \App\Models\WorkOrder $wo */
        $wo = $this->record;

        // Log awal - WO Created
        WorkOrderLog::create([
            'work_order_id' => $wo->id,
            'stage'         => $wo->status,
            'started_at'    => now(),
            'by_user_id'    => Auth::id(),
            'remarks'       => 'Work Order created from Booking #' . ($wo->booking_id ?? 'N/A'),
        ]);

        // Set current_stage_id ke stage pertama
        $firstStage = WorkOrderStage::orderBy('position')->first();
        if ($firstStage) {
            $wo->update(['current_stage_id' => $firstStage->id]);
            
            // Log stage assignment
            WorkOrderLog::create([
                'work_order_id' => $wo->id,
                'stage'         => $firstStage->name,
                'started_at'    => now(),
                'by_user_id'    => Auth::id(),
                'remarks'       => 'Stage set to: ' . $firstStage->name,
            ]);
        }

        // Jika ada mechanic, buat log assignment
        if ($wo->mechanic_id) {
            WorkOrderLog::create([
                'work_order_id' => $wo->id,
                'stage'         => 'Mechanic Assignment',
                'started_at'    => now(),
                'by_user_id'    => Auth::id(),
                'remarks'       => 'Mechanic assigned: ' . ($wo->mechanic->name ?? 'N/A'),
            ]);
        }
    }

    /**
     * Redirect after create
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}