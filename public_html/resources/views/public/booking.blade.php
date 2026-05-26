@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Booking Servis</h1>
        <p class="text-gray-600">Isi form di bawah untuk menjadwalkan servis kendaraan Anda</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('book.store') }}" class="bg-white rounded-lg shadow-sm p-6">
        @csrf

        @guest
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Informasi Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="customer_name"
                        name="customer_name" 
                        value="{{ old('customer_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        No. HP <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="customer_phone"
                        name="customer_phone" 
                        value="{{ old('customer_phone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="08xx xxxx xxxx"
                        required
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="customer_email"
                        name="customer_email" 
                        value="{{ old('customer_email') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="email@example.com"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Konfirmasi booking akan dikirim ke email ini</p>
                </div>
            </div>
        </div>
        @else
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Informasi Pelanggan</h2>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Booking atas nama:</p>
                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
            </div>
            
            <div class="mt-4">
                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                    No. HP (Opsional - untuk konfirmasi)
                </label>
                <input 
                    type="tel" 
                    id="customer_phone"
                    name="customer_phone" 
                    value="{{ old('customer_phone', $user->phone) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="08xx xxxx xxxx"
                >
            </div>
        </div>
        @endguest

        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Informasi Kendaraan</h2>
            
            @auth
                @if($user && $user->vehicles && $user->vehicles->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kendaraan</label>
                    <div class="space-y-2">
                        @foreach($user->vehicles as $vehicle)
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="vehicle_id" 
                                value="{{ $vehicle->id }}"
                                class="mr-3"
                                {{ old('vehicle_id') == $vehicle->id ? 'checked' : '' }}
                            >
                            <div>
                                <p class="font-semibold text-gray-900">{{ $vehicle->plate_number }}</p>
                                <p class="text-sm text-gray-600">{{ $vehicle->full_name }}</p>
                            </div>
                        </label>
                        @endforeach
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="radio" 
                                name="vehicle_id" 
                                value=""
                                class="mr-3"
                                id="new_vehicle_toggle"
                                {{ old('vehicle_id') === '' ? 'checked' : '' }}
                            >
                            <span class="font-semibold text-red-600">+ Tambah Kendaraan Baru</span>
                        </label>
                    </div>
                </div>

                <div id="new_vehicle_form" class="hidden">
                    <p class="text-sm text-gray-600 mb-3">Masukkan data kendaraan baru:</p>
                @endif
            @endauth

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="vehicle_plate" class="block text-sm font-medium text-gray-700 mb-1">
                        Plat Nomor <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="vehicle_plate"
                        name="vehicle_plate" 
                        value="{{ old('vehicle_plate') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 uppercase"
                        placeholder="B 1234 XYZ"
                        required
                    >
                </div>

                <div>
                    <label for="vehicle_brand" class="block text-sm font-medium text-gray-700 mb-1">
                        Merek
                    </label>
                    <input 
                        type="text" 
                        id="vehicle_brand"
                        name="vehicle_brand" 
                        value="{{ old('vehicle_brand') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Toyota, Honda, dll"
                    >
                </div>

                <div>
                    <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-1">
                        Model
                    </label>
                    <input 
                        type="text" 
                        id="vehicle_model"
                        name="vehicle_model" 
                        value="{{ old('vehicle_model') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Avanza, Civic, dll"
                    >
                </div>

                <div>
                    <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun
                    </label>
                    <input 
                        type="number" 
                        id="vehicle_year"
                        name="vehicle_year" 
                        value="{{ old('vehicle_year') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="2020"
                        min="1900"
                        max="{{ date('Y') + 1 }}"
                    >
                </div>
            </div>

            @auth
                @if($user->vehicles && $user->vehicles->count() > 0)
                </div>
                @endif
            @endauth
        </div>

        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b">Jadwal & Layanan</h2>
            
            <div class="mb-6">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">
                    Jadwal Servis <span class="text-red-500">*</span>
                </label>
                <input 
                    type="datetime-local" 
                    id="scheduled_at"
                    name="scheduled_at" 
                    value="{{ old('scheduled_at') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Pilih tanggal dan waktu yang Anda inginkan</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Pilih Layanan <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($services as $service)
                    <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-500 transition">
                        <input 
                            type="checkbox" 
                            name="service_ids[]" 
                            value="{{ $service->id }}"
                            class="mt-1 mr-3"
                            {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}
                        >
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                            @if($service->base_price)
                            {{-- <p class="text-sm text-gray-600">Rp {{ number_format($service->base_price, 0, ',', '.') }}</p> --}}
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">* Pilih minimal 1 layanan</p>
            </div>
        </div>

        <div class="mb-8">
            <label for="complaint_note" class="block text-sm font-medium text-gray-700 mb-1">
                Keluhan / Catatan
            </label>
            <textarea 
                id="complaint_note"
                name="complaint_note" 
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                placeholder="Jelaskan kondisi kendaraan atau keluhan yang Anda alami..."
            >{{ old('complaint_note') }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t">
            <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-900">
                ← Kembali
            </a>
            <button 
                type="submit"
                class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 transition"
            >
                Kirim Booking
            </button>
        </div>
    </form>
</div>

@auth
{{-- JavaScript tidak mengandung kelas warna, jadi tidak ada perubahan --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newVehicleToggle = document.getElementById('new_vehicle_toggle');
    const newVehicleForm = document.getElementById('new_vehicle_form');
    const vehicleRadios = document.querySelectorAll('input[name="vehicle_id"]');
    
    if (newVehicleToggle && newVehicleForm) {
        vehicleRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === '') {
                    newVehicleForm.classList.remove('hidden');
                } else {
                    newVehicleForm.classList.add('hidden');
                }
            });
        });
        
        // Check on page load
        if (newVehicleToggle.checked) {
            newVehicleForm.classList.remove('hidden');
        }
    }
});
</script>
@endauth
@endsection