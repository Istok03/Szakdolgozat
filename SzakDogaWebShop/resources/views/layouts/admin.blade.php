    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <title>Admin felület</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link  href="{{ asset('css/admin.css') }}" rel="stylesheet">
    </head>
    <body>
        @include('partials.navbar')


        
        @if(auth()->check() && auth()->user()->is_admin)
                <button id="sidebarToggle" class="sidebar-toggle">☰</button>
                @include('components.admin.sidebar')
        @endif
        

        <div class="container mt-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Bezárás"></button>
                </div>
            @endif

    <div class="admin-content">
        @yield('content')
    </div>

        </div>

        <script src="{{ asset('js/sidebar.js') }}"></script>
    </body>
    </html>
