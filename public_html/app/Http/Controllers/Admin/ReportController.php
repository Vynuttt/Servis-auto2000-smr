<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BookingService;
use App\Models\User;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Laporan Popularitas Layanan
     */
    public function printServiceReport()
    {
        

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $services = BookingService::with('service')
            ->select('service_id', DB::raw('count(*) as total_bookings'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('service_id')
            ->orderBy('total_bookings', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'service_name' => $item->service->name ?? 'N/A',
                    'service_code' => $item->service->code ?? 'N/A',
                    'total_bookings' => $item->total_bookings,
                ];
            });
            
        $data = [
            'services' => $services,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'logoPath' => public_path('storage/avatars/logoAuto2000.png'), // <-- DITAMBAHKAN
        ];

        $pdf = Pdf::loadView('pdf.admin.service-report', $data);
        $fileName = 'laporan-layanan-' . $endDate->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Laporan Kinerja Mekanik
     */
    public function printMechanicReport()
    {
        

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $mechanics = User::whereHas('roles', function($q) {
                $q->where('slug', 'mechanic');
            })
            ->withCount([
                'assignedWorkOrders as completed_work_orders_count' => function($query) use ($startDate, $endDate) {
                    $query->where('status', 'Done') 
                          ->whereBetween('updated_at', [$startDate, $endDate]);
                }
            ])
            ->orderBy('completed_work_orders_count', 'desc')
            ->get();
            
        $data = [
            'mechanics' => $mechanics,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'logoPath' => public_path('storage/avatars/logoAuto2000.png'), // <-- DITAMBAHKAN
        ];

        $pdf = Pdf::loadView('pdf.admin.mechanic-report', $data);
        $fileName = 'laporan-mekanik-' . $endDate->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Laporan Ringkasan Analitik
     */
    public function printAnalyticsReport()
    {
       

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(30);

        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedBookings = Booking::where('status', 'Completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $totalRevenue = BookingService::whereHas('booking', function($q) use ($startDate, $endDate) {
                $q->where('status', 'Completed')
                  ->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->sum('subtotal');
            
        $data = [
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'totalRevenue' => $totalRevenue,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'logoPath' => public_path('storage/avatars/logoAuto2000.png'), // <-- DITAMBAHKAN
        ];

        $pdf = Pdf::loadView('pdf.admin.analytics-report', $data);
        $fileName = 'laporan-analitik-' . $endDate->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }
}