<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Customer Dashboard' }} - Auto2000</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-red-600">Auto2000</span>
                    </a>
                    
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('customer.dashboard') }}" 
                           class="text-gray-700 hover:text-red-600 font-medium transition
                           {{ request()->routeIs('customer.dashboard') ? 'text-red-600' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('customer.bookings.index') }}" 
                           class="text-gray-700 hover:text-red-600 font-medium transition
                           {{ request()->routeIs('customer.bookings.*') ? 'text-red-600' : '' }}">
                            My Bookings
                        </a>
                        <a href="{{ route('customer.vehicles.index') }}" 
                           class="text-gray-700 hover:text-red-600 font-medium transition
                           {{ request()->routeIs('customer.vehicles.*') ? 'text-red-600' : '' }}">
                            My Vehicles
                        </a>
                    </div>
                </div>

                {{-- <div class="flex items-center gap-4">
                    <a href="{{ route('customer.bookings.create') }}" 
                       class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Booking
                    </a> --}}

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center font-semibold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-1">
                            <a href="{{ route('customer.dashboard') }}" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                🏠 Dashboard
                            </a>
                            <a href="#" 
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                👤 Profile
                            </a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>

                    <button @click="mobileOpen = !mobileOpen" 
                            class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileOpen" 
             x-transition
             class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('customer.dashboard') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    Dashboard
                </a>
                <a href="{{ route('customer.bookings.index') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    My Bookings
                </a>
                <a href="{{ route('customer.vehicles.index') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                    My Vehicles
                </a>
                <a href="{{ route('customer.bookings.create') }}" 
                   class="block px-3 py-2 rounded-lg bg-red-600 text-white text-center font-semibold">
                    + New Booking
                </a>
            </div>
        </div>
    </nav>

    <main>
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">×</button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-gray-600 text-sm">
                    &copy; {{ date('Y') }} Auto2000. All rights reserved.
                </p>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-blue-600">Home</a>
                    <a href="{{ route('track.form') }}" class="text-gray-600 hover:text-blue-600">Track Booking</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Help</a>
                </div>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdown', () => ({
                open: false,
                mobileOpen: false
            }))
        })
    </script>
    
    @stack('scripts') 

</body>
</html>