<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin felület – Istók IT</title>
    <link href="{{ asset('css/admin-panel.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-panel">
    <a class="admin-skip-link" href="#admin-content">Ugrás a tartalomhoz</a>
    <header class="admin-header">
        <div class="admin-header-top">
            @include('partials.brand', ['caption' => 'Adminisztráció'])
            <div class="admin-account">
                <span class="admin-account-name">{{ auth()->user()->name }}</span>
                <a class="admin-shop-link" href="{{ route('home') }}">Webshop megnyitása <span aria-hidden="true">↗</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-logout">Kilépés</button>
                </form>
            </div>
        </div>
        <nav class="admin-nav" aria-label="Adminisztrációs menü">
            @foreach([
                'dashboard' => 'Áttekintés',
                'products' => 'Termékek',
                'orders' => 'Rendelések',
                'users' => 'Felhasználók',
                'categories' => 'Kategóriák',
                'messages' => 'Üzenetek',
            ] as $section => $label)
                @php
                    $active = $section === 'dashboard'
                        ? request()->routeIs('admin.dashboard')
                        : request()->routeIs('admin.'.$section.'.*');
                    $destination = $section === 'dashboard' ? 'admin.dashboard' : 'admin.'.$section.'.index';
                @endphp
                <a href="{{ route($destination) }}" class="admin-nav-link {{ $active ? 'is-active' : '' }}" @if($active) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
    </header>
    <main id="admin-content" class="admin-content" tabindex="-1">
        @include('partials.messages')
        @yield('content')
    </main>
    <footer class="admin-footer">Istók IT · Adminisztráció</footer>
    @stack('scripts')
</body>
</html>
