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
    <a href="{{ route('login') }}">Belépés</a>
    <a href="{{ route(name:'register') }}">Regisztráció</a>
    <a href="{{ route('cart.index') }}">
    <img src="{{ asset('images/shopping-bag.png' )}}" alt="Kosár" class="cart-icon">
</a>
</div>

 
</nav>
