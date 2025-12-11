<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Istok\'s IT store') }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
    <script src="{{ asset('js/filter.js') }}"></script>

<body>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <main>
            @yield('content')
        </main>

        <div class="watermark">
            <p>&copy; 2025 Istok's IT store. Minden jog fenntartva.</p>
        </div>
    </div>
</body>
</html>