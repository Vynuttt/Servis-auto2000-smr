{{-- resources/views/customer/vehicles/edit.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('customer.vehicles.show', $vehicle) }}" 
               class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Kendaraan</h1>
                <p class="text-gray-600 mt-1">Perbarui data kendaraan {{ $vehicle->plate_number }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('customer.vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                    Informasi Dasar
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="plate_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Plat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="plate_number" 
                               id="plate_number"
                               value="{{ old('plate_number', $vehicle->plate_number) }}"
                               placeholder="Contoh: B 1234 XYZ"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition uppercase @error('plate_number') border-red-500 @enderror"
                               required>
                        @error('plate_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="brand" class="block text-sm font-semibold text-gray-700 mb-2">
                            Merek <span class="text-red-500">*</span>
                        </label>
                        <select name="brand" 
                                id="brand"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('brand') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Merek</option>
                            <option value="Toyota" {{ old('brand', $vehicle->brand) == 'Toyota' ? 'selected' : '' }}>Toyota</option>
                            <option value="Honda" {{ old('brand', $vehicle->brand) == 'Honda' ? 'selected' : '' }}>Honda</option>
                            <option value="Mitsubishi" {{ old('brand', $vehicle->brand) == 'Mitsubishi' ? 'selected' : '' }}>Mitsubishi</option>
                            <option value="Suzuki" {{ old('brand', $vehicle->brand) == 'Suzuki' ? 'selected' : '' }}>Suzuki</option>
                            <option value="Daihatsu" {{ old('brand', $vehicle->brand) == 'Daihatsu' ? 'selected' : '' }}>Daihatsu</option>
                            <option value="Nissan" {{ old('brand', $vehicle->brand) == 'Nissan' ? 'selected' : '' }}>Nissan</option>
                            <option value="Mazda" {{ old('brand', $vehicle->brand) == 'Mazda' ? 'selected' : '' }}>Mazda</option>
                            <option value="Other" {{ old('brand', $vehicle->brand) == 'Other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-semibold text-gray-700 mb-2">
                            Model <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="model" 
                               id="model"
                               value="{{ old('model', $vehicle->model) }}"
                               placeholder="Contoh: Avanza, Civic, Xpander"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('model') border-red-500 @enderror"
                               required>
                        @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="year" 
                               id="year"
                               value="{{ old('year', $vehicle->year) }}"
                               min="1900"
                               max="{{ date('Y') + 1 }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('year') border-red-500 @enderror"
                               required>
                        @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                            Warna
                        </label>
                        <input type="text" 
                               name="color" 
                               id="color"
                               value="{{ old('color', $vehicle->color) }}"
                               placeholder="Contoh: Hitam, Putih, Silver"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('color') border-red-500 @enderror">
                        @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                    Spesifikasi Teknis (Opsional)
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vin" class="block text-sm font-semibold text-gray-700 mb-2">
                            VIN (Vehicle Identification Number)
                        </label>
                        <input type="text" 
                               name="vin" 
                               id="vin"
                               value="{{ old('vin', $vehicle->vin) }}"
                               placeholder="17 karakter"
                               maxlength="17"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition uppercase @error('vin') border-red-500 @enderror">
                        @error('vin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="engine_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Mesin
                        </label>
                        <input type="text" 
                               name="engine_number" 
                               id="engine_number"
                               value="{{ old('engine_number', $vehicle->engine_number) }}"
                               placeholder="Nomor mesin kendaraan"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition uppercase @error('engine_number') border-red-500 @enderror">
                        @error('engine_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transmission" class="block text-sm font-semibold text-gray-700 mb-2">
                            Transmisi
                        </label>
                        <select name="transmission" 
                                id="transmission"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('transmission') border-red-500 @enderror">
                            <option value="">Pilih Transmisi</option>
                            <option value="manual" {{ old('transmission', $vehicle->transmission) == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="automatic" {{ old('transmission', $vehicle->transmission) == 'automatic' ? 'selected' : '' }}>Automatic</option>
                            <option value="cvt" {{ old('transmission', $vehicle->transmission) == 'cvt' ? 'selected' : '' }}>CVT</option>
                        </select>
                        @error('transmission')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fuel_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Bahan Bakar
                        </label>
                        <select name="fuel_type" 
                                id="fuel_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('fuel_type') border-red-500 @enderror">
                            <option value="">Pilih Bahan Bakar</option>
                            <option value="gasoline" {{ old('fuel_type', $vehicle->fuel_type) == 'gasoline' ? 'selected' : '' }}>Bensin</option>
                            <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="electric" {{ old('fuel_type', $vehicle->fuel_type) == 'electric' ? 'selected' : '' }}>Electric</option>
                        </select>
                        @error('fuel_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @if($vehicle->bookings()->whereIn('status', ['Booked', 'In Progress'])->count() > 0)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Perhatian!</p>
                        <p>Kendaraan ini memiliki booking aktif. Perubahan data mungkin mempengaruhi booking yang sedang berjalan.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Tips Pengisian Data:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan data yang diubah sudah benar</li>
                            <li>Perubahan nomor plat harus unik dan tidak sama dengan kendaraan lain</li>
                            <li>Data teknis bersifat opsional namun disarankan untuk dilengkapi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg hover:shadow-xl">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ route('customer.vehicles.show', $vehicle) }}"
                   class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>

<script>
// Auto uppercase untuk plate number
document.getElementById('plate_number').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Auto uppercase untuk VIN dan engine number
document.getElementById('vin')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
document.getElementById('engine_number')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endsection