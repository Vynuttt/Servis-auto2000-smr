<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\WorkOrder;

class TrackingController extends Controller
{
    /**
     * Menampilkan form untuk memasukkan kode tracking
     */
    public function form()
    {
        return view('public.track');
    }

    /**
     * Menampilkan hasil tracking berdasarkan kode
     * Bisa menerima Booking Code (BK-xxx) atau WO Number (WO-xxx)
     */
    public function show($code)
    {
        $booking = null;
        $workOrder = null;

        // Deteksi apakah ini Booking Code atau WO Number
        if (str_starts_with($code, 'BK-')) {
            // Cari berdasarkan booking_code
            $booking = Booking::where('booking_code', $code)
                ->with(['customer', 'vehicle', 'workOrder.mechanic', 'workOrder.stall'])
                ->firstOrFail();
            
            $workOrder = $booking->workOrder;
        } 
        elseif (str_starts_with($code, 'WO-')) {
            // Cari berdasarkan wo_number
            $workOrder = WorkOrder::where('wo_number', $code)
                ->with(['booking.customer', 'booking.vehicle', 'mechanic', 'stall'])
                ->firstOrFail();
            
            $booking = $workOrder->booking;
        } 
        else {
            abort(404, 'Invalid tracking code format. Use BK-xxx or WO-xxx');
        }

        return view('public.track-show', compact('booking', 'workOrder'));
    }

    /**
     * (Opsional) Mengembalikan data JSON untuk polling/AJAX
     */
    public function json($code)
    {
        // Sama seperti show(), tapi return JSON
        if (str_starts_with($code, 'BK-')) {
            $booking = Booking::where('booking_code', $code)
                ->with('workOrder')
                ->firstOrFail();
            $workOrder = $booking->workOrder;
        } 
        elseif (str_starts_with($code, 'WO-')) {
            $workOrder = WorkOrder::where('wo_number', $code)
                ->with('booking')
                ->firstOrFail();
            $booking = $workOrder->booking;
        } 
        else {
            return response()->json(['error' => 'Invalid code'], 400);
        }

        $data = [
            'booking_code' => $booking->booking_code,
            'booking_status' => $booking->status,
            'wo_number' => $workOrder?->wo_number,
            'wo_status' => $workOrder?->status,
            'mechanic' => $workOrder?->mechanic?->name,
            'stall' => $workOrder?->stall?->code,
            'created_at' => $booking->created_at->format('d M Y H:i'),
        ];

        return response()->json($data);
    }
}