{{-- resources/views/customer/vehicles/index.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kendaraan Saya</h1>
            <p class="text-gray-600 mt-2">Kelola semua kendaraan yang terdaftar</p>
        </div>
        <a href="{{ route('customer.vehicles.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kendaraan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Kendaraan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $vehicles->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl">🚗</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aktif</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $vehicles->count() }}</p>
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
                    <p class="text-sm text-gray-600 mb-1">Total Booking</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $vehicles->sum(fn($v) => $v->bookings_count ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($vehicles->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Daftar Kendaraan</h2>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kendaraan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Plat Nomor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe/Model
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tahun
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">🚗</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $vehicle->brand ?? 'Toyota' }} {{ $vehicle->model ?? '' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $vehicle->full_name ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                {{ $vehicle->plate_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vehicle->type ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $vehicle->year ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('customer.vehicles.show', $vehicle) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                Lihat
                            </a>
                            <a href="{{ route('customer.vehicles.edit', $vehicle) }}" 
                               class="text-green-600 hover:text-green-900 mr-3">
                                Edit
                            </a>
                            <form action="{{ route('customer.vehicles.destroy', $vehicle) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="md:hidden divide-y divide-gray-200">
            @foreach($vehicles as $vehicle)
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl">🚗</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $vehicle->brand ?? 'Toyota' }} {{ $vehicle->model ?? '' }}</h3>
                            <p class="text-sm text-gray-600">{{ $vehicle->full_name ?? '' }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">
                        Active
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Plat Nomor:</span>
                        <span class="font-semibold text-red-600">{{ $vehicle->plate_number }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Tipe:</span>
                        <span class="font-medium text-gray-900">{{ $vehicle->type ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Tahun:</span>
                        <span class="font-medium text-gray-900">{{ $vehicle->year ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('customer.vehicles.show', $vehicle) }}" 
                       class="flex-1 px-4 py-2 bg-red-600 text-white text-center rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        Detail
                    </a>
                    <a href="{{ route('customer.vehicles.edit', $vehicle) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        Edit
                    </a>
                    <form action="{{ route('customer.vehicles.destroy', $vehicle) }}" 
                          method="POST" 
                          class="flex-1"
                          onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @else
    <div class="bg-white rounded-xl shadow-md p-12">
        <div class="text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Kendaraan</h3>
            <p class="text-gray-600 mb-6">
                Tambahkan kendaraan Anda untuk memudahkan proses booking servis
            </p>
            <a href="{{ route('customer.vehicles.create') }}" 
               class="inline-flex items-center gap-2 px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kendaraan Pertama
            </a>
        </div>
    </div>
    @endif

</div>
@endsection