<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PrintController extends Controller
{
    /**
     * Cetak PDF untuk Booking
     */
    public function printBooking($id)
    {
        $booking = Booking::with([
            'customer',
            'vehicle',
            'bookingServices.service',
            'workOrder.mechanic',
            'workOrder.stall'
        ])->findOrFail($id);

        $data = [
            'booking' => $booking,
            'logoPath' => public_path('storage/avatars/logoAuto2000.png'),
            'printedAt' => Carbon::now()->format('d M Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.admin.booking-detail', $data);
        $fileName = 'booking-' . $booking->booking_code . '.pdf';
        
        return $pdf->download($fileName);
    }

    /**
     * Cetak PDF untuk Work Order
     */
    public function printWorkOrder($id)
    {
        $workOrder = WorkOrder::with([
            'booking.customer',
            'booking.vehicle',
            'booking.bookingServices.service',
            'mechanic',
            'stall',
            'currentStage',
            'logs.user'
        ])->findOrFail($id);

        $data = [
            'workOrder' => $workOrder,
            'logoPath' => public_path('storage/avatars/logoAuto2000.png'),
            'printedAt' => Carbon::now()->format('d M Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.admin.work-order-detail', $data);
        $fileName = 'work-order-' . $workOrder->wo_number . '.pdf';
        
        return $pdf->download($fileName);
    }
}