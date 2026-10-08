<nav class="navbar">
    <div class="logo">
        <a href="/">Istok's IT store</a>
    </div>

    <button class="menu-toggle" id="menuToggle">☰</button>

    <ul class="nav-links" id="navLinks">
        <li><a href="/">Főoldal</a></li>
        <li><a href="/products">Termékek</a></li> 
        <li><a href="/offers">Akciók</a></li>
        <li><a href="/contact">Kapcsolat</a></li> 
    </ul>

    <div class="auth-links">
        <div class="navbar-right">
            @auth
                <a href="{{ route('profile.edit') }}">Profilom</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn logout-button">Kilépés</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Belépés</a>
                <a href="{{ route('register') }}" class="btn btn-light">Regisztráció</a>
            @endauth

            <a href="{{ route('cart.index') }}">
                <img src="{{ asset('images/shopping-bag.png') }}" alt="Kosár" class="cart-icon">
                @php
                    $cartQuantity = auth()->check()
                        ? \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity')
                        : collect(session('cart', []))->sum('quantity');
                @endphp
                <span class="cart-badge cart-count" @if(!$cartQuantity) hidden @endif>{{ $cartQuantity }}</span>
            </a>
        </div>
    </div>
</nav>

<script src="{{ asset('js/mobil_menu.js') }}"></script>
