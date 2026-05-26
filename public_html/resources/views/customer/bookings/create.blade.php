@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('customer.bookings.index') }}"
                class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Booking Baru</h1>
                <p class="text-gray-600 mt-1">Jadwalkan servis kendaraan Anda</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('customer.bookings.store') }}" method="POST" id="booking-form">
            @csrf

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                    Pilih Kendaraan
                </h2>

                <div class="space-y-4">
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="vehicle_option" value="existing" class="peer sr-only" 
                                {{ old('vehicle_option', $vehicles->count() > 0 ? 'existing' : 'new') == 'existing' ? 'checked' : '' }}>
                            <div class="p-4 border-2 rounded-lg peer-checked:border-red-600 peer-checked:bg-red-50 hover:border-red-300 transition">
                                <p class="font-semibold text-gray-900">Kendaraan Terdaftar</p>
                                <p class="text-sm text-gray-600">Pilih dari kendaraan Anda</p>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="vehicle_option" value="new" class="peer sr-only"
                                {{ old('vehicle_option') == 'new' || $vehicles->count() == 0 ? 'checked' : '' }}>
                            <div class="p-4 border-2 rounded-lg peer-checked:border-red-600 peer-checked:bg-red-50 hover:border-red-300 transition">
                                <p class="font-semibold text-gray-900">Kendaraan Baru</p>
                                <p class="text-sm text-gray-600">Input data kendaraan</p>
                            </div>
                        </label>
                    </div>

                    <div id="existing-vehicle-section" class="hidden">
                        @if($vehicles->count() > 0)
                        <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <select name="vehicle_id" id="vehicle_id"
                            class="w-full px-4 py-3 border @error('vehicle_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }} - {{ $vehicle->full_name }} ({{ $vehicle->year }})
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <a href="{{ route('customer.vehicles.create') }}" class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800">
                            + Tambah kendaraan baru
                        </a>
                        @else
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-amber-800">Anda belum memiliki kendaraan terdaftar.</p>
                            <a href="{{ route('customer.vehicles.create') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                Daftarkan kendaraan →
                            </a>
                        </div>
                        @endif
                    </div>

                    <div id="new-vehicle-section" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="vehicle_plate" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nomor Plat <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="vehicle_plate" id="vehicle_plate"
                                    value="{{ old('vehicle_plate') }}"
                                    placeholder="Contoh: B 1234 XYZ"
                                    class="w-full px-4 py-3 border @error('vehicle_plate') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition uppercase">
                                @error('vehicle_plate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_brand" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Merek
                                </label>
                                <input type="text" name="vehicle_brand" id="vehicle_brand"
                                    value="{{ old('vehicle_brand') }}"
                                    placeholder="Toyota, Honda, dll"
                                    class="w-full px-4 py-3 border @error('vehicle_brand') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                @error('vehicle_brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Model
                                </label>
                                <input type="text" name="vehicle_model" id="vehicle_model"
                                    value="{{ old('vehicle_model') }}"
                                    placeholder="Avanza, Civic, dll"
                                    class="w-full px-4 py-3 border @error('vehicle_model') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                @error('vehicle_model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_year" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tahun
                                </label>
                                <input type="number" name="vehicle_year" id="vehicle_year"
                                    value="{{ old('vehicle_year', date('Y')) }}"
                                    min="1900" max="{{ date('Y') + 1 }}"
                                    class="w-full px-4 py-3 border @error('vehicle_year') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                @error('vehicle_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                    Pilih Jadwal
                </h2>

                <div>
                    <label for="scheduled_at" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal & Waktu Servis <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                        value="{{ old('scheduled_at') }}"
                        min="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-3 border @error('scheduled_at') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        required>
                    @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimal 2 jam dari sekarang</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                    Pilih Layanan
                </h2>

                @if($services->count() > 0)
                <div class="space-y-3">
                    @foreach($services as $service)
                    <label class="flex items-start gap-4 p-4 border-2 rounded-lg cursor-pointer hover:border-red-300 has-[:checked]:border-red-600 has-[:checked]:bg-red-50 transition">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                            {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}
                            class="mt-1 w-5 h-5 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                            @if($service->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $service->description }}</p>
                            @endif
                            {{-- <p class="text-lg font-bold text-blue-600 mt-2"> Rp {{ number_format($service->base_price ?? 0, 0, ',', '.') }}
                            </p> --}}
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('service_ids')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @else
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <p class="text-gray-600">Tidak ada layanan tersedia saat ini.</p>
                </div>
                @endif
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold">4</span>
                    Keluhan / Catatan (Opsional)
                </h2>

                <div>
                    <label for="complaint_note" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jelaskan keluhan atau permintaan khusus
                    </label>
                    <textarea name="complaint_note" id="complaint_note" rows="4"
                        placeholder="Contoh: Mesin berbunyi aneh saat kecepatan tinggi, AC kurang dingin, dll."
                        class="w-full px-4 py-3 border @error('complaint_note') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">{{ old('complaint_note') }}</textarea>
                    @error('complaint_note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Penting:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Booking harus dibuat minimal 2 jam sebelum jadwal servis</li>
                            <li>Anda akan menerima kode tracking untuk memantau progres servis</li>
                            <li>Pastikan hadir tepat waktu sesuai jadwal yang dipilih</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg hover:shadow-xl">
                    📅 Buat Booking
                </button>
                <a href="{{ route('customer.bookings.index') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
{{-- JavaScript tidak mengandung kelas warna, jadi tidak ada perubahan --}}
<script>
console.log('Script loaded!'); // Test if script runs

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded!'); // Test if DOM ready
    
    const form = document.getElementById('booking-form');
    console.log('Form found:', form); // Check if form exists
    
    const vehicleOptionRadios = document.querySelectorAll('input[name="vehicle_option"]');
    const existingSection = document.getElementById('existing-vehicle-section');
    const newSection = document.getElementById('new-vehicle-section');
    const vehicleIdSelect = document.getElementById('vehicle_id');

    function toggleVehicleSections() {
        const selectedOption = document.querySelector('input[name="vehicle_option"]:checked')?.value;
        console.log('Selected option:', selectedOption);
        
        if (selectedOption === 'existing') {
            existingSection?.classList.remove('hidden');
            newSection?.classList.add('hidden');
            
            if (vehicleIdSelect) vehicleIdSelect.removeAttribute('disabled');
            ['vehicle_plate', 'vehicle_brand', 'vehicle_model', 'vehicle_year'].forEach(name => {
                const input = document.getElementById(name);
                if (input) {
                    input.setAttribute('disabled', 'disabled');
                    input.removeAttribute('required');
                }
            });
        } else {
            existingSection?.classList.add('hidden');
            newSection?.classList.remove('hidden');
            
            if (vehicleIdSelect) {
                vehicleIdSelect.setAttribute('disabled', 'disabled');
                vehicleIdSelect.removeAttribute('required');
            }
            ['vehicle_plate', 'vehicle_brand', 'vehicle_model', 'vehicle_year'].forEach(name => {
                const input = document.getElementById(name);
                if (input) input.removeAttribute('disabled');
            });
            
            const plateInput = document.getElementById('vehicle_plate');
            if (plateInput) plateInput.setAttribute('required', 'required');
        }
    }

    // Initialize
    toggleVehicleSections();

    // Listen to radio changes
    vehicleOptionRadios.forEach(radio => {
        radio.addEventListener('change', toggleVehicleSections);
    });

    // Auto uppercase plate
    const plateInput = document.getElementById('vehicle_plate');
    if (plateInput) {
        plateInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    }

    // Form submit handler
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            
            const selectedOption = document.querySelector('input[name="vehicle_option"]:checked')?.value;
            const serviceChecked = document.querySelectorAll('input[name="service_ids[]"]:checked').length;
            
            console.log('Services checked:', serviceChecked);
            
            // Check services
            if (serviceChecked === 0) {
                e.preventDefault();
                alert('Pilih minimal satu layanan!');
                return false;
            }

            // Check vehicle
            if (selectedOption === 'existing') {
                const vehicleId = vehicleIdSelect?.value;
                if (!vehicleId) {
                    e.preventDefault();
                    alert('Pilih kendaraan terlebih dahulu!');
                    return false;
                }
            } else {
                const plate = document.getElementById('vehicle_plate')?.value.trim();
                if (!plate) {
                    e.preventDefault();
                    alert('Nomor plat kendaraan harus diisi!');
                    return false;
                }
            }

            // Check date
            const scheduledAt = document.getElementById('scheduled_at')?.value;
            if (!scheduledAt) {
                e.preventDefault();
                alert('Pilih tanggal dan waktu servis!');
                return false;
            }

            console.log('Form validation passed! Submitting...');
            // Allow form to submit naturally
        });
    } else {
        console.error('Form not found!');
    }
});
</script>
@endpush