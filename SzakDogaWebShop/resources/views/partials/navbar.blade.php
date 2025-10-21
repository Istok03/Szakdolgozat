<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Webshop</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Bejelentkezés</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Regisztráció</a></li>
                @else
                    <li class="nav-item"><a class="nav-link" href="#">{{ Auth::user()->name }}</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit">Kilépés</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
