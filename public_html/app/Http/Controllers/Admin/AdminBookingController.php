<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, WorkOrder, WorkOrderApproval};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- 1. TAMBAHKAN INI

class AdminBookingController extends Controller
{
    public function convertToWorkOrder(Booking $booking, Request $r)
    {
        // ... (kode ini sudah benar, tidak diubah) ...
        $data = $r->validate([
            'mechanic_id' => ['nullable','exists:users,id'],
            'stall_id'    => ['nullable','exists:stalls,id'],
        ]);

        return DB::transaction(function () use ($booking, $data) {
            $wo = WorkOrder::create([
                'wo_number'    => 'WO-'.$booking->booking_code,
                'booking_id'   => $booking->id,
                'customer_id'  => $booking->customer_id,
                'vehicle_id'   => $booking->vehicle_id,
                'mechanic_id'  => $data['mechanic_id'] ?? null,
                'stall_id'     => $data['stall_id'] ?? null,
                'planned_start'=> $booking->scheduled_at,
                'status'       => WorkOrder::S_PLANNED,
                'priority'     => 'Regular',
            ]);

            $booking->update([
                'status'        => 'Converted',
                'work_order_id' => $wo->id,
            ]);

            return response()->json(['ok' => true, 'wo_id' => $wo->id]);
        });
    }

    public function approve(WorkOrderApproval $approval)
    {
        $wo = $approval->workOrder;
        // 2. UBAH DARI auth()->id() MENJADI Auth::id()
        $wo->approveRequest($approval, Auth::id(), null);
        return response()->json(['ok' => true]);
    }

    public function reject(WorkOrderApproval $approval, Request $r)
    {
        $data = $r->validate(['note' => ['required','string','max:500']]);
        // 3. UBAH DARI auth()->id() MENJADI Auth::id()
        $approval->workOrder->rejectRequest($approval, Auth::id(), $data['note']);
        return response()->json(['ok' => true]);
    }
}
