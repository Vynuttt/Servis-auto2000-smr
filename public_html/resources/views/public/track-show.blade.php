@extends('layouts.public')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('track.form') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Cari Booking Lain
        </a>

        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $booking->booking_code }}</h1>
                    <p class="text-gray-600">
                        @if($workOrder)
                            Work Order: <span class="font-mono font-semibold text-red-600">{{ $workOrder->wo_number }}</span>
                        @else
                            Booking ID: {{ $booking->id }}
                        @endif
                    </p>
                </div>
                <span class="px-4 py-2 text-lg font-semibold rounded-full
                    {{ $booking->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $booking->status === 'Checked-In' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $booking->status === 'In Service' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $booking->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $booking->status === 'Cancelled' ? 'bg-red-100 text-red-800' : '' }}
                ">
                    {{ $booking->status }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-2">Customer</p>
                    <p class="font-semibold text-gray-900">{{ $booking->customer_name ?? $booking->customer->name ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-600 mb-2">Kendaraan</p>
                    <p class="font-semibold text-gray-900">{{ $booking->vehicle_plate }} - {{ $booking->vehicle_model }}</p>
                </div>
            </div>
        </div>

        @if($workOrder)
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-8 h-8 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Status Progress Servis
            </h2>

            <div class="relative">
                <div class="absolute top-12 left-0 w-full h-1 bg-gray-200"></div>
                
                @php
                    $statuses = ['Checked-In', 'Waiting', 'In-Progress', 'QC', 'Wash', 'Final', 'Done'];
                    $currentIndex = array_search($workOrder->status, $statuses);
                    $progress = $currentIndex !== false ? (($currentIndex + 1) / count($statuses)) * 100 : 0;
                @endphp
                <div class="absolute top-12 left-0 h-1 bg-red-600 transition-all duration-500" style="width: {{ $progress }}%"></div>

                <div class="relative grid grid-cols-7 gap-2">
                    @foreach(['Checked-In', 'Waiting', 'In-Progress', 'QC', 'Wash', 'Final', 'Done'] as $status)
                    @php
                        $statusIndex = array_search($status, $statuses);
                        $isActive = $statusIndex <= $currentIndex;
                        $isCurrent = $workOrder->status === $status;
                    @endphp
                    <div class="flex flex-col items-center">
                        <div class="w-24 h-24 rounded-xl flex items-center justify-center mb-2 transition-all duration-300
                            {{ $isCurrent ? 'bg-red-600 text-white shadow-lg scale-110' : '' }}
                            {{ $isActive && !$isCurrent ? 'bg-green-500 text-white' : '' }}
                            {{ !$isActive ? 'bg-gray-300 text-gray-600' : '' }}
                        ">
                            @if($isActive)
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-center
                            {{ $isCurrent ? 'text-red-600' : '' }}
                            {{ $isActive && !$isCurrent ? 'text-green-600' : '' }}
                            {{ !$isActive ? 'text-gray-400' : '' }}
                        ">
                            {{ $status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 p-6 bg-red-50 rounded-xl border-2 border-red-200">
                <h3 class="font-bold text-red-900 mb-2">Status Saat Ini:</h3>
                <p class="text-red-800">
                    @switch($workOrder->status)
                        @case('Checked-In')
                            Kendaraan Anda sudah terdaftar dan menunggu proses servis.
                            @break
                        @case('Waiting')
                            Kendaraan sedang dalam antrian untuk diproses.
                            @break
                        @case('In-Progress')
                            Mekanik sedang mengerjakan servis kendaraan Anda.
                            @break
                        @case('QC')
                            Kendaraan sedang dalam proses quality control.
                            @break
                        @case('Wash')
                            Kendaraan sedang dalam proses pencucian.
                            @break
                        @case('Final')
                            Kendaraan sudah hampir selesai!
                            @break  
                        @case('Done')
                            Kendaraan sudah selesai dan siap diambil. Terima kasih!
                            @break
                    @endswitch
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mekanik
                </h3>
                <p class="text-lg font-semibold text-gray-900">{{ $workOrder->mechanic->name ?? 'Belum ditentukan' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Stall
                </h3>
                <p class="text-lg font-semibold text-gray-900">{{ $workOrder->stall->code ?? 'Belum ditentukan' }}</p>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Menunggu Konfirmasi</h3>
            <p class="text-gray-600 mb-6">
                Booking Anda sedang diproses. Work Order akan dibuat segera.
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <strong>Jadwal Servis:</strong> {{ $booking->scheduled_at ? $booking->scheduled_at->format('d F Y, H:i') : $booking->booking_date }}
                </p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Informasi Booking</h3>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Tanggal Booking</span>
                    <span class="font-semibold">{{ $booking->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Jadwal Servis</span>
                    <span class="font-semibold">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d F Y, H:i') : $booking->booking_date }}</span>
                </div>
                @if($booking->complaint_note)
                <div class="border-b pb-2">
                    <span class="text-gray-600 block mb-1">Keluhan</span>
                    <p class="font-semibold text-gray-900">{{ $booking->complaint_note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection