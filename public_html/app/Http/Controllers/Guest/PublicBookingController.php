<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Booking, Service, User, Vehicle};
use Illuminate\Support\Facades\{DB, Auth, Mail};
use Illuminate\Validation\ValidationException;

class PublicBookingController extends Controller
{
    /**
     * Landing page
     */
    public function landing()
    {
        return view('public.landing');
    }

    /**
     * Show booking form
     */
    public function create()
    {
        $services = Service::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $user = Auth::user(); // Akan null jika guest

        return view('public.booking', compact('services', 'user'));
    }

    /**
     * Store booking (Guest or Logged-in)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validation rules
        $rules = [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'scheduled_at' => ['required', 'date', 'after_or_equal:today'],
            'complaint_note' => ['nullable', 'string', 'max:1000'],
        ];

        // Guest booking: require contact info
        if (!$user) {
            $rules = array_merge($rules, [
                'customer_name' => ['required', 'string', 'max:100'],
                'customer_email' => ['required', 'email', 'max:120'],
                'customer_phone' => ['required', 'string', 'max:30'],
                'vehicle_plate' => ['required', 'string', 'max:20'],
                'vehicle_brand' => ['nullable', 'string', 'max:50'],
                'vehicle_model' => ['nullable', 'string', 'max:60'],
                'vehicle_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            ]);
        } else {
            // Logged-in: require vehicle selection or new vehicle data
            $rules['vehicle_id'] = ['nullable', 'integer', 'exists:vehicles,id'];
            $rules['vehicle_plate'] = ['required_without:vehicle_id', 'string', 'max:20'];
            $rules['vehicle_brand'] = ['nullable', 'string', 'max:50'];
            $rules['vehicle_model'] = ['nullable', 'string', 'max:60'];
            $rules['vehicle_year'] = ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)];
        }

        $validated = $request->validate($rules);

        try {
            return DB::transaction(function () use ($request, $validated, $user) {
                
                // === 1. HANDLE CUSTOMER ===
                $customerId = null;
                $customerName = null;
                $customerEmail = null;
                $customerPhone = $validated['customer_phone'] ?? null;

                if ($user) {
                    // Logged-in user
                    $customerId = $user->id;
                    $customerName = $user->name;
                    $customerEmail = $user->email;
                    $customerPhone = $validated['customer_phone'] ?? $user->phone;
                } else {
                    // Guest: Find or create customer
                    $customerEmail = $validated['customer_email'];
                    $customerName = $validated['customer_name'];
                    
                    // Cek apakah email sudah terdaftar
                    $existingCustomer = User::where('email', $customerEmail)->first();
                    
                    if ($existingCustomer) {
                        $customerId = $existingCustomer->id;
                    } else {
                        // Create new customer account (inactive, perlu verifikasi)
                        $newCustomer = User::create([
                            'name' => $customerName,
                            'email' => $customerEmail,
                            'phone' => $customerPhone,
                            'password' => bcrypt(\Illuminate\Support\Str::random(16)), // random password
                            'is_active' => false, // Set inactive until verified
                        ]);

                        // Attach customer role
                        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
                        if ($customerRole) {
                            $newCustomer->roles()->attach($customerRole->id);
                        }

                        $customerId = $newCustomer->id;
                    }
                }

                // === 2. HANDLE VEHICLE ===
                $vehicleId = null;

                if ($user && !empty($validated['vehicle_id'])) {
                    // User memilih vehicle existing
                    $vehicleId = $validated['vehicle_id'];
                } else {
                    // Find or create vehicle by plate
                    $vehicle = Vehicle::findOrCreateByPlate(
                        $validated['vehicle_plate'],
                        $customerId,
                        [
                            'brand' => $validated['vehicle_brand'] ?? null,
                            'model' => $validated['vehicle_model'] ?? null,
                            'year' => $validated['vehicle_year'] ?? null,
                        ]
                    );
                    $vehicleId = $vehicle->id;
                }

                // === 3. CREATE BOOKING ===
                $booking = Booking::create([
                    'booking_code' => Booking::generateBookingCode(),
                    'tracking_code' => Booking::generateTrackingCode(),
                    'customer_id' => $customerId,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'vehicle_id' => $vehicleId,
                    'vehicle_plate' => $validated['vehicle_plate'],
                    'vehicle_model' => ($validated['vehicle_brand'] ?? '') . ' ' . ($validated['vehicle_model'] ?? ''),
                    'scheduled_at' => $validated['scheduled_at'],
                    'booking_date' => \Carbon\Carbon::parse($validated['scheduled_at'])->toDateString(),
                    'booking_time' => \Carbon\Carbon::parse($validated['scheduled_at'])->toTimeString(),
                    'service_type' => 'Multiple Services', // Fix: tambahkan ini
                    'complaint_note' => $validated['complaint_note'] ?? null,
                    'status' => 'Booked',
                    'source_channel' => 'Web',
                    'sla_minutes' => 120, // default 2 jam
                ]);

                // === 4. ATTACH SERVICES (Pivot Table) ===
                $services = Service::whereIn('id', $validated['service_ids'])->get();
                
                foreach ($services as $service) {
                    $booking->bookingServices()->create([
                        'service_id' => $service->id,
                        'qty' => 1,
                        'price' => $service->base_price ?? 0,
                        'subtotal' => $service->base_price ?? 0,
                    ]);
                }

                // === 5. SEND EMAIL CONFIRMATION (Optional) ===
                // TODO: Implement email notification
                // Mail::to($customerEmail)->send(new BookingConfirmation($booking));

                // === 6. REDIRECT ===
                return redirect()
                    ->route('track.show', $booking->booking_code)
                    ->with('success', 'Booking berhasil dibuat! Simpan kode booking Anda: ' . $booking->booking_code);
            });

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}