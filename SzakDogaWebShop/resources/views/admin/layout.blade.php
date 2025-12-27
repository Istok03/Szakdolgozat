<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin felület</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <header>
        <h1>Admin panel</h1>
        <nav>
            <a href="{{ route('admin.users.index') }}">Felhasználók</a>
            <a href="{{ route('admin.products.index') }}">Termékek</a>
            <a href="{{ route('admin.orders.index') }}">Rendelések</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
