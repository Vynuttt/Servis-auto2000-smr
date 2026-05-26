<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use App\Models\{Booking, Service, Vehicle};
// 1. Ini adalah baris yang memanggil DomPDF
use Barryvdh\DomPDF\Facade\Pdf; 

class CustomerBookingController extends Controller
{
    /**
     * Display a listing of customer's bookings
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Perbaikan relasi yang sudah Anda lakukan
        $query = Booking::where('customer_id', $user->id)
            ->with(['vehicle', 'bookingServices.service']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create()
    {
        $user = Auth::user();
        
        $services = Service::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $vehicles = Vehicle::where('customer_id', $user->id)
            ->where('is_active', true)
            ->orderBy('plate_number')
            ->get();
            

        return view('customer.bookings.create', compact('services', 'vehicles', 'user'));
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'vehicle_plate' => ['required_without:vehicle_id', 'string', 'max:20'],
            'vehicle_brand' => ['nullable', 'string', 'max:50'],
            'vehicle_model' => ['nullable', 'string', 'max:60'],
            'vehicle_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'scheduled_at' => ['required', 'date', 'after_or_equal:today'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'complaint_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $user) {
                
                /** @var \App\Models\Vehicle $vehicle */
                $vehicle = null; 
                
                if (!empty($validated['vehicle_id'])) {
                    $vehicle = Vehicle::find($validated['vehicle_id']);
                    
                    if (!$vehicle || $vehicle->customer_id !== $user->id) {
                         throw new \Exception('Kendaraan yang dipilih tidak valid.');
                    }

                } else {
                    $vehicle = Vehicle::findOrCreateByPlate(
                        $validated['vehicle_plate'],
                        $user->id,
                        [
                            'brand' => $validated['vehicle_brand'] ?? null,
                            'model' => $validated['vehicle_model'] ?? null,
                            'year' => $validated['vehicle_year'] ?? null,
                            'is_active' => true, 
                        ]
                    );
                }

                $booking = Booking::create([
                    'booking_code' => Booking::generateBookingCode(),
                    'tracking_code' => Booking::generateTrackingCode(),
                    'customer_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone,
                    'vehicle_id' => $vehicle->id,
                    'vehicle_plate' => $vehicle->plate_number, 
                    'vehicle_model' => $vehicle->full_name ?? trim(($vehicle->brand ?? '') . ' ' . ($vehicle->model ?? '')),
                    'scheduled_at' => $validated['scheduled_at'],
                    'booking_date' => \Carbon\Carbon::parse($validated['scheduled_at'])->toDateString(),
                    'booking_time' => \Carbon\Carbon::parse($validated['scheduled_at'])->toTimeString(),
                    'service_type' => 'Multiple Services',
                    'complaint_note' => $validated['complaint_note'] ?? null,
                    'status' => 'Booked',
                    'source_channel' => 'Web',
                    'sla_minutes' => 120,
                ]);

                $services = Service::whereIn('id', $validated['service_ids'])->get();
                
                foreach ($services as $service) {
                    $booking->bookingServices()->create([
                        'service_id' => $service->id,
                        'qty' => 1,
                        'price' => $service->base_price ?? 0,
                    ]);
                }

                return redirect()
                    ->route('customer.bookings.show', $booking)
                    ->with('success', 'Booking berhasil dibuat! Kode booking: ' . $booking->booking_code);
            });

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        if ($booking->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Perbaikan relasi yang sudah Anda lakukan
        $booking->load(['vehicle', 'bookingServices.service', 'mechanic', 'workOrder']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Cancel the specified booking
     */
    public function cancel(Booking $booking)
    {
        if ($booking->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        try {
            $booking->cancel('Cancelled by customer');
            
            return back()->with('success', 'Booking berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fungsi BARU untuk download PDF menggunakan DomPDF
     */
    public function downloadPDF(Booking $booking)
    {
        // 1. Pastikan booking ini milik customer yang sedang login
        if ($booking->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Load relasi (penting untuk data di PDF)
        // Gunakan relasi yang sudah benar
        $booking->load(['vehicle', 'bookingServices.service', 'workOrder']);

        // 3. Load view Blade yang berisi template PDF
        // Ganti 'customer.bookings.pdf_invoice' dengan path view Anda
        $pdf = Pdf::loadView('customer.bookings.pdf_invoice', [
            'booking' => $booking
        ]);

        // 4. Buat nama file
        $fileName = 'invoice-' . $booking->booking_code . '.pdf';

        // 5. Kirim file PDF ke browser untuk di-download
        return $pdf->download($fileName);
            
        // Jika ingin ditampilkan di browser (preview), ganti baris di atas dengan:
        // return $pdf->stream($fileName);
    }
}