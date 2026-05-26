<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Display a listing of the customer's vehicles.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // DEBUG - Uncomment untuk test
        // dd([
        //     'user_id' => $user->id,
        //     'raw_query' => Vehicle::where('customer_id', $user->id)->toSql(),
        //     'vehicles_from_db' => Vehicle::where('customer_id', $user->id)->get(),
        //     'vehicles_from_relation' => $user->vehicles,
        // ]);
        
        $vehicles = $user->vehicles()
            ->with('bookings')
            ->withCount('bookings')
            ->latest()
            ->get();
        
        return view('customer.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        return view('customer.vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => [
                'required',
                'string',
                'max:20',
                'unique:vehicles,plate_number',
                'regex:/^[A-Z0-9\s]+$/i'
            ],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'vin' => 'nullable|string|max:17|unique:vehicles,vin',
            'engine_number' => 'nullable|string|max:50',
            'transmission' => 'nullable|in:manual,automatic,cvt',
            'fuel_type' => 'nullable|in:gasoline,diesel,hybrid,electric',
        ]);

        // Normalize plate number (uppercase, remove extra spaces)
        $validated['plate_number'] = strtoupper(
            preg_replace('/\s+/', ' ', trim($validated['plate_number']))
        );

        // Add customer_id and is_active
        $validated['customer_id'] = Auth::id();
        $validated['is_active'] = true;

        Vehicle::create($validated);

        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        // Load relationships
        $vehicle->load(['bookings' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('customer.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        return view('customer.vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $validated = $request->validate([
            'plate_number' => [
                'required',
                'string',
                'max:20',
                'unique:vehicles,plate_number,' . $vehicle->id,
                'regex:/^[A-Z0-9\s]+$/i'
            ],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'vin' => 'nullable|string|max:17|unique:vehicles,vin,' . $vehicle->id,
            'engine_number' => 'nullable|string|max:50',
            'transmission' => 'nullable|in:manual,automatic,cvt',
            'fuel_type' => 'nullable|in:gasoline,diesel,hybrid,electric',
        ]);

        // Normalize plate number
        $validated['plate_number'] = strtoupper(
            preg_replace('/\s+/', ' ', trim($validated['plate_number']))
        );

        $vehicle->update($validated);

        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui!');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        // Check if vehicle has active bookings
        $activeBookings = $vehicle->bookings()
            ->whereIn('status', ['Booked', 'In Progress'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'Tidak dapat menghapus kendaraan yang masih memiliki booking aktif.');
        }

        $vehicle->delete();

        return redirect()->route('customer.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus!');
    }

    /**
     * Check if the authenticated user owns the vehicle.
     */
    private function authorizeVehicle(Vehicle $vehicle): void
    {
        if ($vehicle->customer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }
    }
}