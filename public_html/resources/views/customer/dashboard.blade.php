@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-gray-600 mt-2">Kelola booking dan kendaraan Anda di sini</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBookings ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $activeBookings ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Vehicles</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalVehicles ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $completedBookings ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('customer.bookings.create') }}" 
           class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Booking Baru</h3>
                    <p class="text-red-100">Jadwalkan servis kendaraan</p>
                </div>
                <svg class="w-8 h-8 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('customer.vehicles.index') }}" 
           class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Kendaraan Saya</h3>
                    <p class="text-red-100">Kelola data kendaraan</p>
                </div>
                <svg class="w-8 h-8 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('track.form') }}" 
           class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Lacak Booking</h3>
                    <p class="text-green-100">Pantau status servis</p>
                </div>
                <svg class="w-8 h-8 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Booking Terbaru</h2>
            <a href="{{ route('customer.bookings.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                Lihat Semua →
            </a>
        </div>

        @if($recentBookings && $recentBookings->count() > 0)
        <div class="space-y-4">
            @foreach($recentBookings as $booking)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-red-300 hover:bg-red-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                {{ $booking->booking_code }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                {{ $booking->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $booking->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $booking->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $booking->status }}
                            </span>
                        </div>
                        <p class="text-gray-900 font-medium">{{ $booking->vehicle->plate_number ?? 'N/A' }} - {{ $booking->vehicle->full_name ?? '' }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->scheduled_at?->format('d M Y, H:i') ?? $booking->booking_date }}</p>
                    </div>
                    <a href="{{ route('customer.bookings.show', $booking) }}" 
                       class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 mb-4">Belum ada booking</p>
            <a href="{{ route('customer.bookings.create') }}" 
               class="inline-block px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                Buat Booking Pertama
            </a>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Kendaraan Saya</h2>
            <a href="{{ route('customer.vehicles.create') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                + Tambah Kendaraan
            </a>
        </div>

        @if($vehicles && $vehicles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($vehicles as $vehicle)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-red-300 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🚗</span>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                        Active
                    </span>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $vehicle->plate_number }}</h3>
                <p class="text-sm text-gray-600">{{ $vehicle->full_name }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <p class="text-gray-500 mb-4">Belum ada kendaraan terdaftar</p>
            <a href="{{ route('customer.vehicles.create') }}" 
               class="inline-block px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                Tambah Kendaraan
            </a>
        </div>
        @endif
    </div>
</div>
@endsection