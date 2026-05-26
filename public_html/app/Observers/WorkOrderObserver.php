<?php

namespace App\Observers;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Log; // Kita bisa tambahkan Log untuk debugging

class WorkOrderObserver
{
    /**
     * Handle the WorkOrder "updated" event.
     */
    public function updated(WorkOrder $workOrder)
    {
        // Cek apakah status berubah
        if ($workOrder->isDirty('status') && $workOrder->booking) {
            
            // Tulis log untuk memastikan ini berjalan
            Log::info("WorkOrderObserver: WO ID {$workOrder->id} status diubah ke {$workOrder->status}. Memulai sinkronisasi booking.");

            $this->syncBookingStatus($workOrder);
        }
    }

    /**
     * Sinkronisasi status Booking berdasarkan Work Order status
     */
    private function syncBookingStatus(WorkOrder $workOrder): void
    {
        $bookingStatus = match($workOrder->status) {
            'Pending' => 'Booked',
            'Waiting' => 'Checked-In',
            
            // --- INI YANG DIUBAH ---
            // Menggunakan 'In-Service' (dengan dash)
            // Sesuaikan ini jika nama di database Anda berbeda
            'In-Progress' => 'In-Service',
            'QC' => 'In-Service',
            'Wash' => 'In-Service',
            
            'Final' => 'Ready', // Pastikan 'Ready' juga ada di ENUM Anda
            'Done' => 'Completed',
            'Cancelled' => 'Cancelled',
            default => $workOrder->booking->status, // Tidak berubah jika status unknown
        };

        // Update booking status jika berbeda
        if ($workOrder->booking->status !== $bookingStatus) {
            Log::info("WorkOrderObserver: Mengubah Booking ID {$workOrder->booking_id} dari '{$workOrder->booking->status}' menjadi '{$bookingStatus}'");
            
            $workOrder->booking->update([
                'status' => $bookingStatus
            ]);
        } else {
             Log::info("WorkOrderObserver: Status Booking ID {$workOrder->booking_id} sudah '{$bookingStatus}'. Tidak ada update.");
        }
    }
}

