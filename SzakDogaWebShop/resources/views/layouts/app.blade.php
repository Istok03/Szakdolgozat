<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Istok\'s IT store') }}</title>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
    <script src="{{ asset('js/filter.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>

<body>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')
        
       <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline-secondary">
        <i class="bi bi-box-arrow-right"></i> Kilépés
    </button>
</form>

</form>

        <main>
            @yield('content')
        </main>




        <div class="watermark">
            <p>&copy; 2025 Istok's IT store. Minden jog fenntartva.</p>
        </div>
    </div>



    
<div class="cart-notification" id="cart-notification">
    Sikeresen hozzáadva a kosárhoz
</div>

</body>
</html>