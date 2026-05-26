@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Bookings</h1>
            <p class="text-gray-600 mt-2">Kelola semua booking servis Anda</p>
        </div>
        <a href="{{ route('customer.bookings.create') }}" 
           class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg">
            + New Booking
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-2 mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('customer.bookings.index') }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ !request('status') ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
           All
        </a>
        <a href="{{ route('customer.bookings.index', ['status' => 'Booked']) }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'Booked' ? 'bg-yellow-100 text-yellow-800' : 'text-gray-700 hover:bg-gray-100' }}">
           Booked
        </a>
        
        <a href="{{ route('customer.bookings.index', ['status' => 'In-Service']) }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'In-Service' ? 'bg-red-100 text-red-800' : 'text-gray-700 hover:bg-gray-100' }}">
           In Service
        </a>
        
        <a href="{{ route('customer.bookings.index', ['status' => 'Ready']) }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'Ready' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
           Ready
        </a>
        
        <a href="{{ route('customer.bookings.index', ['status' => 'Completed']) }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'Completed' ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-gray-100' }}">
           Completed
        </a>
        <a href="{{ route('customer.bookings.index', ['status' => 'Cancelled']) }}" 
           class="px-4 py-2 rounded-lg font-medium whitespace-nowrap {{ request('status') === 'Cancelled' ? 'bg-red-100 text-red-800' : 'text-gray-700 hover:bg-gray-100' }}">
           Cancelled
        </a>
    </div>

    @if($bookings && $bookings->count() > 0)
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-xl font-bold text-gray-900">{{ $booking->booking_code }}</h3>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                {{ $booking->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $booking->status === 'Checked-In' ? 'bg-blue-100 text-blue-800' : '' }}
                                
                                {{ $booking->status === 'In-Service' ? 'bg-red-100 text-red-800' : '' }}
                                
                                {{ $booking->status === 'Ready' ? 'bg-blue-100 text-blue-800' : '' }}
                                
                                {{ $booking->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $booking->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $booking->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600 mb-1">Kendaraan</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $booking->vehicle->plate_number ?? $booking->vehicle_plate }}
                                </p>
                                <p class="text-gray-600">{{ $booking->vehicle->full_name ?? $booking->vehicle_model }}</p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">Jadwal</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y, H:i') : $booking->booking_date }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">Layanan</p>
                                <p class="font-semibold text-gray-900">
                                    @if($booking->services && $booking->services->count() > 0)
                                        {{ $booking->services->pluck('name')->implode(', ') }}
                                    @else
                                        {{ $booking->service_type }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($booking->complaint_note)
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Keluhan:</strong> {{ $booking->complaint_note }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <div class="ml-4 flex flex-col gap-2">
                        <a href="{{ route('customer.bookings.show', $booking) }}" 
                           class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition text-center">
                           Detail
                        </a>
                        
                        @if($booking->status === 'Booked')
                        <button 
                            onclick="if(confirm('Yakin ingin membatalkan booking ini?')) { document.getElementById('cancel-form-{{ $booking->id }}').submit(); }"
                            class="px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition text-center">
                            Cancel
                        </button>
                        <form id="cancel-form-{{ $booking->id }}" 
                              method="POST" 
                              action="{{ route('customer.bookings.cancel', $booking) }}" 
                              class="hidden">
                            @csrf
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Booking</h3>
        <p class="text-gray-600 mb-6">Anda belum memiliki booking servis. Mulai sekarang!</p>
        <a href="{{ route('customer.bookings.create') }}" 
           class="inline-block px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg">
           Buat Booking Pertama
        </a>
    </div>
    @endif
</div>
@endsection