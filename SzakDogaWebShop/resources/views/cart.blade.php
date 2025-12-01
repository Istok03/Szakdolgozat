@extends('layouts.app')

@section('content')
    <div class="cart-page">
        <h1>Kosár</h1>
        <p>Itt láthatod a kosárba tett termékeidet.</p>

        <div class="cart-items">
            <div class="cart-item">
                <img src="{{ asset('images/products/laptop.png') }}" alt="Laptop">
                <div class="item-details">
                    <h3>Gaming Laptop</h3>
                    <p>Ár: 299 000 Ft</p>
                    <p>Mennyiség: 1</p>
                </div>
                <button class="remove-btn">Eltávolítás</button>
            </div>

            <div class="cart-item">
                <img src="{{ asset('images/products/mouse.png') }}" alt="Egér">
                <div class="item-details">
                    <h3>RGB Egér</h3>
                    <p>Ár: 9 990 Ft</p>
                    <p>Mennyiség: 2</p>
                </div>
                <button class="remove-btn">Eltávolítás</button>
            </div>
        </div>

        <div class="cart-summary">
            <h2>Összesen: 319 980 Ft</h2>
            <div class="cart-actions">
                <a href="{{ route('products') }}" class="continue-btn">Vásárlás folytatása</a>
                <a href="{{ route('payment') }}" class="checkout-btn">Tovább a fizetéshez</a>
            </div>
        </div>
    </div>
@endsection
