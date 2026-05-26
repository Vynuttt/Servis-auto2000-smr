{{-- resources/views/customer/vehicles/show.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('customer.vehicles.index') }}" 
                   class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Kendaraan</h1>
                    <p class="text-gray-600 mt-1">Informasi lengkap kendaraan Anda</p>
                </div>
            </div>
            <a href="{{ route('customer.vehicles.edit', $vehicle) }}"
               class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-lg">
                ✏️ Edit Kendaraan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-xl p-8 text-white">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <span class="text-4xl">🚗</span>
                        </div>
                        <div>
                            {{-- Asumsi Anda punya accessor 'full_name' di model Vehicle --}}
                            <h2 class="text-3xl font-bold">{{ $vehicle->full_name ?? ($vehicle->brand . ' ' . $vehicle->model) }}</h2>
                            <p class="text-blue-100 text-lg mt-1">{{ $vehicle->color ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full">
                        Active
                    </span>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <p class="text-blue-100 text-sm mb-1">Nomor Plat</p>
                    <p class="text-3xl font-bold">{{ $vehicle->plate_number }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Informasi Detail</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Merek</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->brand }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Model</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->model }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tahun</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->year }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Warna</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $vehicle->color ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if($vehicle->vin || $vehicle->engine_number || $vehicle->transmission || $vehicle->fuel_type)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Spesifikasi Teknis</h3>
                
                <div class="space-y-4">
                    @if($vehicle->vin)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600">VIN</span>
                        <span class="font-semibold text-gray-900 font-mono">{{ $vehicle->vin }}</span>
                    </div>
                    @endif

                    @if($vehicle->engine_number)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600">Nomor Mesin</span>
                        <span class="font-semibold text-gray-900 font-mono">{{ $vehicle->engine_number }}</span>
                    </div>
                    @endif

                    @if($vehicle->transmission)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600">Transmisi</span>
                        <span class="font-semibold text-gray-900 capitalize">{{ $vehicle->transmission }}</span>
                    </div>
                    @endif

                    @if($vehicle->fuel_type)
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-600">Bahan Bakar</span>
                        <span class="font-semibold text-gray-900 capitalize">
                            @if($vehicle->fuel_type === 'gasoline') Bensin
                            @elseif($vehicle->fuel_type === 'diesel') Diesel
                            @elseif($vehicle->fuel_type === 'hybrid') Hybrid
                            @elseif($vehicle->fuel_type === 'electric') Electric
                            @else {{ $vehicle->fuel_type }}
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Riwayat Booking</h3>
                    <a href="{{ route('customer.bookings.create') }}?vehicle={{ $vehicle->id }}"
                       class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                        + Booking Baru
                    </a>
                </div>

                @if($vehicle->bookings && $vehicle->bookings->count() > 0)
                <div class="space-y-3">
                    @foreach($vehicle->bookings as $booking)
                    <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        {{ $booking->booking_code }}
                                    </span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $booking->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $booking->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        {{ $booking->status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    📅 {{ $booking->scheduled_at?->format('d M Y, H:i') ?? $booking->booking_date }}
                                </p>
                            </div>
                            <a href="{{ route('customer.bookings.show', $booking) }}"
                               class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 mb-4">Belum ada booking untuk kendaraan ini</p>
                    
                    {{-- 
                    INI ADALAH PERBAIKANNYA: 
                    Mengganti $booking->id (yang tidak ada di sini) menjadi $vehicle->id
                    --}}
                    <a href="{{ route('customer.bookings.create') }}?vehicle={{ $vehicle->id }}"
                       class="inline-block px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Buat Booking Pertama
                    </a>
                </div>
                @endif
            </div>

        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">Total Booking</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">{{ $vehicle->bookings->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">Selesai</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">
                            {{ $vehicle->bookings->where('status', 'Completed')->count() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">Aktif</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">
                            {{ $vehicle->bookings->whereIn('status', ['Booked', 'In Progress'])->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('customer.bookings.create') }}?vehicle={{ $vehicle->id }}"
                       class="block w-full px-4 py-3 bg-blue-600 text-white text-center font-semibold rounded-lg hover:bg-blue-700 transition">
                        📅 Booking Servis
                    </a>
                    
                    <a href="{{ route('customer.vehicles.edit', $vehicle) }}"
                       class="block w-full px-4 py-3 bg-green-600 text-white text-center font-semibold rounded-lg hover:bg-green-700 transition">
                        ✏️ Edit Data
                    </a>
                    
                    <form action="{{ route('customer.vehicles.destroy', $vehicle) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus kendaraan ini? Aksi ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="block w-full px-4 py-3 bg-red-600 text-white text-center font-semibold rounded-lg hover:bg-red-700 transition">
                            🗑️ Hapus Kendaraan
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi</p>
                        <p>Data kendaraan ini digunakan untuk proses booking dan servis Anda.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection