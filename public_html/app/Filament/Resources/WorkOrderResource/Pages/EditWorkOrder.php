<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Models\WorkOrderLog;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/** @property \App\Models\WorkOrder $record */
class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;

    /**
     * Recalc planned_finish jika planned_start / sla_minutes berubah.
     * Tutup log stage lama & buka log baru bila status berubah.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // === 1) Recalc planned_finish jika perlu ===
        // (hanya kalau planned_start ada; SLA default 60 jika kosong)
        if (!empty($data['planned_start'])) {
            $sla = (int)($data['sla_minutes'] ?? $this->record->sla_minutes ?? 60);
            $start = $data['planned_start'] instanceof Carbon
                ? $data['planned_start']
                : Carbon::parse($data['planned_start']);

            $data['planned_finish'] = $start->copy()->addMinutes($sla);
        }

        // === 2) Jika status berubah, tutup log aktif & buka log baru ===
        $oldStatus = $this->record->status;
        $newStatus = $data['status'] ?? $oldStatus;

        if ($oldStatus !== $newStatus) {
            $woId = $this->record->getKey();

            // Tutup log yang masih "terbuka" (finished_at = null)
            WorkOrderLog::where('work_order_id', $woId)
                ->whereNull('finished_at')
                ->latest('started_at')
                ->limit(1)
                ->update(['finished_at' => now()]);

            // Buat log baru utk status baru
            WorkOrderLog::create([
                'work_order_id' => $woId,
                'stage'         => $newStatus,
                'started_at'    => now(),
                'by_user_id'    => Auth::id(),
                'remarks'       => 'Status changed via edit',
            ]);
        }

        return $data;
    }
}
