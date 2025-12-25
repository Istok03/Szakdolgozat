<nav class="navbar">
        <div class="logo">
            <a href="/">Istok's IT store</a>
        </div>
<ul class="nav-links">
    <li><a href="/">Főoldal</a></li>
    <li><a href="/products">Termékek</a></li> 
    <li><a href="/offers">Akciók</a></li>
    <li><a href="/contact">Kapcsolat</a></li> 
</ul>



<div class="auth-links">
    <div class="navbar-right">
    @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn logout-button">Kilépés</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Belépés</a>
        <a href="{{ route('register') }}" class="btn btn-light">Regisztráció</a>
    @endauth

     <a href="{{ route('cart.index') }}">
    <img src="{{ asset('images/shopping-bag.png' )}}" alt="Kosár" class="cart-icon">
    @if(session('cart') && count(session('cart')) > 0)
        <span class="cart-badge">{{ count(session('cart')) }}</span>
    @endif
    </a>

</div>

   
</div>

 
</nav>
