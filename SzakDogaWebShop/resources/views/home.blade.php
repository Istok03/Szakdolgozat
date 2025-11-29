@extends('layouts.app')

@section('content')
    <div class="hero">
            <h1>Üdvözöljük Istók informatikai webboltjában</h1>
            <p>Fedezd fel a legjobb ajánlatokat</p>
            <a href="/offers" class="cta-button">Akciók megtekintése</a>
    </div>

    <div class="featured-products">
            <h2>Kiemelt termékek</h2>
            <div class="product-grid">
                     <div class="product-card">
                <img src="{{ asset('images/products/laptop.png') }}" alt="Laptop">
                <h3>Gaming Laptop</h3>
                <p>Akciós ár: 299 000 Ft</p>
                <button>Kosárba</button>
            </div>
            <div class="product-card">
                <img src="{{ asset('images/products/mouse.png') }}" alt="Egér">
                <h3>RGB Egér</h3>
                <p>Akciós ár: 9 990 Ft</p>
                <button>Kosárba</button>
            </div>
            </div>
    </div>

@endsection