<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Istok\'s IT store') }}</title>
    @isset($slot)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endisset
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
    
<body>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

@if(Request::is('products') || Request::is('offers'))
    @php
        $categories = \App\Models\Category::all();
    @endphp

    <div class="navbar-categories">
    <button id="categoryToggle" class="category-button">
        Kategóriák
    </button>

    <div id="categoryPanel" class="category-panel">
        @foreach($categories as $category)
            <a href="{{ url('/products?category=' . $category->id) }}" class="category-list-item">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>

<div class="filter-toggle">
    <button id="filterToggle" class="filter-btn">Szűrő</button>
</div>

@endif


        
       @if(auth()->check() && auth()->user()->is_admin)
        <button id="sidebarToggle" class="sidebar-toggle">☰</button>
        @include('components.admin.sidebar')
       @endif


        <main>
            @include('partials.messages')
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <div class="cart-notification" id="cart-notification" role="status" aria-live="polite">
            Sikeresen hozzáadva a kosárhoz
        </div>

<script src="{{ asset('js/filter.js') }}"></script>
<script src="{{ asset('js/cart.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>

<div class="watermark">
            @include('layouts.footer')
</div>
     </div>
</body>
   
   
</html>
