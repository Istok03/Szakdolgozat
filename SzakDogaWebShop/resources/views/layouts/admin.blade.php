<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin felület</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.navbar')

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Bezárás"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
