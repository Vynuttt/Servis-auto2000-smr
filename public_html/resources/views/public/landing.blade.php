@extends('layouts.public')

@section('content')
<div class="bg-gradient-to-r from-red-600 to-red-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl font-bold mb-6">
                    Servis Kendaraan Anda dengan Mudah
                </h1>
                <p class="text-xl mb-8 text-red-100">
                    Booking online, pantau progress real-time, dan dapatkan layanan terbaik untuk kendaraan Anda.
                </p>
                <div class="flex gap-4">
                    @auth
                    <a href="{{ route('customer.dashboard') }}" 
                       class="px-8 py-4 bg-white text-red-600 font-semibold rounded-lg hover:bg-red-50 transition shadow-lg">
                        🏠 Dashboard Saya
                    </a>
                    @else
                    <a href="{{ route('login') }}" 
                       class="px-8 py-4 bg-white text-red-600 font-semibold rounded-lg hover:bg-red-50 transition shadow-lg">
                        🔐 Login / Daftar
                    </a>
                    @endauth
                    <a href="{{ route('book.create') }}" 
                       class="px-8 py-4 bg-red-700 text-white font-semibold rounded-lg hover:bg-red-600 transition border-2 border-white">
                        🚗 Booking Sekarang
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-2xl">✓</span>
                            </div>
                            <div>
                                <h3 class="font-semibold">Booking Online 24/7</h3>
                                <p class="text-sm text-red-100">Kapan saja, dimana saja</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-2xl">⚡</span>
                            </div>
                            <div>
                                <h3 class="font-semibold">Proses Cepat</h3>
                                <p class="text-sm text-red-100">Tanpa antrian lama</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <span class="text-2xl">👨‍🔧</span>
                            </div>
                            <div>
                                <h3 class="font-semibold">Mekanik Profesional</h3>
                                <p class="text-sm text-red-100">Berpengalaman & tersertifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
            <p class="text-xl text-gray-600">Berbagai layanan servis untuk kendaraan Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <span class="text-3xl">🔧</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">Ganti Oli</h3>
                <p class="text-gray-600 mb-4">Servis ganti oli berkala untuk performa mesin optimal</p>
                <p class="text-red-600 font-semibold">Mulai dari Rp 150.000</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                    <span class="text-3xl">🔩</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">Tune Up</h3>
                <p class="text-gray-600 mb-4">Tune up lengkap untuk performa maksimal kendaraan</p>
                <p class="text-green-600 font-semibold">Mulai dari Rp 300.000</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <span class="text-3xl">❄️</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4">AC Service</h3>
                <p class="text-gray-600 mb-4">Perbaikan dan perawatan AC kendaraan</p>
                <p class="text-red-600 font-semibold">Mulai dari Rp 250.000</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('book.create') }}" 
               class="inline-block px-8 py-4 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition shadow-lg">
                Lihat Semua Layanan →
            </a>
        </div>
    </div>
</div>

<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Cara Kerja</h2>
            <p class="text-xl text-gray-600">3 langkah mudah untuk booking servis</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
                    1
                </div>
                <h3 class="text-xl font-semibold mb-4">Pilih Layanan</h3>
                <p class="text-gray-600">Pilih jenis layanan yang Anda butuhkan dan tentukan jadwal</p>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
                    2
                </div>
                <h3 class="text-xl font-semibold mb-4">Konfirmasi</h3>
                <p class="text-gray-600">Terima konfirmasi booking dan kode untuk tracking</p>
            </div>

            <div class="text-center">
                <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
                    3
                </div>
                <h3 class="text-xl font-semibold mb-4">Pantau Progress</h3>
                <p class="text-gray-600">Lacak status servis kendaraan Anda secara real-time</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-gradient-to-r from-red-600 to-red-800 text-white py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-6">Siap untuk Servis Kendaraan Anda?</h2>
        <p class="text-xl mb-8 text-red-100">
            Dapatkan layanan terbaik dengan booking online sekarang juga!
        </p>
        <div class="flex gap-4 justify-center">
            @guest
            <a href="{{ route('login') }}" 
               class="px-8 py-4 bg-white text-red-600 font-semibold rounded-lg hover:bg-red-50 transition shadow-lg">
                Login / Register
            </a>
            @else
            <a href="{{ route('customer.dashboard') }}" 
               class="px-8 py-4 bg-white text-red-600 font-semibold rounded-lg hover:bg-red-50 transition shadow-lg">
                Dashboard Saya
            </a>
            @endguest
            <a href="{{ route('book.create') }}" 
               class="px-8 py-4 bg-red-700 text-white font-semibold rounded-lg hover:bg-red-600 transition border-2 border-white">
                Booking Sekarang
            </a>
        </div>
    </div>
</div>

<div class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-5xl font-bold text-red-600 mb-2">500+</div>
                <p class="text-gray-600">Customer Puas</p>
            </div>
            <div>
                <div class="text-5xl font-bold text-green-600 mb-2">1000+</div>
                <p class="text-gray-600">Servis Selesai</p>
            </div>
            <div>
                <div class="text-5xl font-bold text-blue-600 mb-2">10+</div>
                <p class="text-gray-600">Mekanik Ahli</p>
            </div>
            <div>
                <div class="text-5xl font-bold text-orange-600 mb-2">4.8</div>
                <p class="text-gray-600">Rating Customer</p>
            </div>
        </div>
    </div>
</div>
@endsection