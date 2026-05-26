@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Bookings
    </a>

    <!-- Header -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $booking->booking_code }}</h1>
                <p class="text-gray-600">Tracking Code: <span class="font-mono font-semibold">{{ $booking->tracking_code }}</span></p>
            </div>
            
            <div class="flex flex-col items-end gap-3">
                <span class="px-4 py-2 text-lg font-semibold rounded-full
                    {{ $booking->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $booking->status === 'Checked-In' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $booking->status === 'In-Service' ? 'bg-purple-100 text-purple-800' : '' }} {{-- Diperbaiki --}}
                    {{ $booking->status === 'Ready' ? 'bg-blue-100 text-blue-800' : '' }} {{-- Ditambahkan --}}
                    {{ $booking->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $booking->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}
                ">
                    {{ $booking->status }}
                </span>

                <!-- ================== TOMBOL BARU DI SINI ================== -->
                @if(in_array($booking->status, ['Ready', 'Completed']))
                <a href="{{ route('customer.bookings.download', $booking) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-800 transition text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Cetak Bukti Servis
                </a>
                @endif
                <!-- ================== AKHIR TOMBOL BARU ================== -->
            </div>

        </div>

        <!-- Actions -->
        @if($booking->status === 'Booked')
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button 
                onclick="if(confirm('Yakin ingin membatalkan booking ini?')) { document.getElementById('cancel-form').submit(); }"
                class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                Batalkan Booking
            </button>
            <form id="cancel-form" method="POST" action="{{ route('customer.bookings.cancel', $booking) }}" class="hidden">
                @csrf
            </form>
        </div>
        @endif
    </div>

    <!-- Booking Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Vehicle Info -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Informasi Kendaraan
            </h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Plat Nomor</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->vehicle->plate_number ?? $booking->vehicle_plate }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Model</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->vehicle->full_name ?? $booking->vehicle_model }}</p>
                </div>
                @if($booking->vehicle && $booking->vehicle->year)
                <div>
                    <p class="text-sm text-gray-600">Tahun</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->vehicle->year }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Schedule Info -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Servis
            </h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Tanggal</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $booking->scheduled_at ? $booking->scheduled_at->format('d F Y') : $booking->booking_date }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Waktu</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $booking->scheduled_at ? $booking->scheduled_at->format('H:i') : $booking->booking_time }}
                    </p>
                </div>
                @if($booking->mechanic)
                <div>
                    <p class="text-sm text-gray-600">Mekanik</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->mechanic->name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Layanan yang Dipilih
        </h2>
        
        @if($booking->services && $booking->services->count() > 0)
        <div class="space-y-3">
            @foreach($booking->services as $service)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                    @if($service->description)
                    <p class="text-sm text-gray-600">{{ $service->description }}</p>
                    @endif
                </div>
                
                {{-- Harga per layanan disembunyikan --}}
                {{--
                <p class="text-lg font-bold text-blue-600">
                    {{ $service->formatted_price }}
                </p>
                --}}
            </div>
            @endforeach
            
            {{-- Total Estimasi disembunyikan --}}
            {{--
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                <p class="font-bold text-gray-900 text-lg">Total Estimasi</p>
                <p class="text-2xl font-bold text-blue-600">
                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                </p>
            </div>
            --}}
        </div>
        @else
        <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-600">{{ $booking->service_type }}</p>
        </div>
        @endif
    </div>

    <!-- Complaint Note -->
    @if($booking->complaint_note)
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            Keluhan / Catatan
        </h2>
        <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
            <p class="text-gray-700">{{ $booking->complaint_note }}</p>
        </div>
    </div>
    @endif

    <!-- Work Order Status (if exists) -->
    @if($booking->workOrder)
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Work Order Status
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-blue-100 text-sm mb-1">WO Number</p>
                <p class="text-xl font-bold">{{ $booking->workOrder->wo_number }}</p>
            </div>
            <div>
                <p class="text-blue-100 text-sm mb-1">Status</p>
                <p class="text-xl font-bold">{{ $booking->workOrder->status }}</p>
            </div>
        </div>
        <a href="{{ route('track.show', $booking->booking_code) }}" 
           class="mt-4 inline-block px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
           Track Progress →
        </a>
    </div>
    @endif

    <!-- Timeline / Metadata -->
    <div class="bg-white rounded-xl shadow-md p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Tambahan</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Dibuat pada:</span>
                <span class="font-semibold">{{ $booking->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Terakhir diupdate:</span>
                <span class="font-semibold">{{ $booking->updated_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Channel:</span>
                <span class="font-semibold">{{ $booking->source_channel }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

