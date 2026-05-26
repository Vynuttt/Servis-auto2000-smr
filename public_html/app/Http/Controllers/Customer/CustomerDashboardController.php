<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Booking, Vehicle};

class CustomerDashboardController extends Controller
{
    /**
     * Show customer dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get stats
        $totalBookings = Booking::where('customer_id', $user->id)->count();
        
        $activeBookings = Booking::where('customer_id', $user->id)
            ->whereIn('status', ['Booked', 'Confirmed', 'Checked-In', 'In Service'])
            ->count();
        
        $completedBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'Completed')
            ->count();
        
        $totalVehicles = Vehicle::where('customer_id', $user->id)
            ->where('is_active', true)
            ->count();

        // Get recent bookings (last 5)
        $recentBookings = Booking::where('customer_id', $user->id)
            ->with(['vehicle'])
            ->latest()
            ->limit(5)
            ->get();

        // Get user vehicles
        $vehicles = Vehicle::where('customer_id', $user->id)
            ->where('is_active', true)
            ->latest()
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact(
            'totalBookings',
            'activeBookings',
            'completedBookings',
            'totalVehicles',
            'recentBookings',
            'vehicles'
        ));
    }
}