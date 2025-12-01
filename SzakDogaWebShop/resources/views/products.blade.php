@extends('layouts.app')

@section('content')
    <div class="products-page">
        <h1>Termékeink</h1>
        <p>Válogass az informatikai eszközök széles kínálatából</p>

        <div class="product-grid">
            <div class="product-card">
                <img src="{{ asset('images/products/laptop.png') }}" alt="Laptop">
                <h3>Gaming Laptop</h3>
                <p>Ár: 349 000 Ft</p>
                <button>Kosárba</button>
            </div>

            <div class="product-card">
                <img src="{{ asset('images/products/mouse.png') }}" alt="Egér">
                <h3>RGB Egér</h3>
                <p>Ár: 12 990 Ft</p>
                <button>Kosárba</button>
            </div>

            <div class="product-card">
                <img src="{{ asset('images/products/keyboard.png') }}" alt="Billentyűzet">
                <h3>Mechanikus Billentyűzet</h3>
                <p>Ár: 24 990 Ft</p>
                <button>Kosárba</button>
            </div>

            <div class="product-card">
                <img src="{{ asset('images/products/headset.png') }}" alt="Fejhallgató">
                <h3>Gaming Headset</h3>
                <p>Ár: 19 990 Ft</p>
                <button>Kosárba</button>
            </div>
        </div>
    </div>
@endsection