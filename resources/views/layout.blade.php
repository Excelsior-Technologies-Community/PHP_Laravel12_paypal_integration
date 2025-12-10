<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PayPal Integration') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="{{ route('payments.create') }}" class="text-xl font-bold">
                    <i class="fab fa-paypal mr-2"></i> PayPal Integration
                </a>
                <div class="space-x-4">
                    <a href="{{ route('payments.create') }}" class="hover:text-blue-200">
                        <i class="fas fa-credit-card mr-1"></i> New Payment
                    </a>
                    <a href="{{ route('payments.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-history mr-1"></i> Payment History
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Secure PayPal payment integration with webhook support</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>