<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'WebsiteAuto2000' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="font-extrabold text-lg text-red-600">Auto2000</a>
            
            <div class="flex items-center gap-6">
                <a href="{{ route('book.create') }}" class="text-sm text-red-600 hover:underline">Booking</a>
                <a href="{{ route('track.form') }}" class="text-sm text-blue-600 hover:underline">Track</a>
                
                @auth
                    <a href="{{ route('customer.dashboard') }}" class="text-sm text-red-600 hover:underline font-semibold">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
                @endauth
            </div>
        </div>
    </nav>
    
    <main class="py-8">
        @yield('content')
    </main>
    
    <footer class="mt-auto py-6 text-center text-sm text-gray-500 border-t border-gray-200">
        <p>&copy; {{ date('Y') }} Auto2000. All rights reserved.</p>
    </footer>
    
</body>
</html>