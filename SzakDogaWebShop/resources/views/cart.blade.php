@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush

@section('content')
    <h1>Kosár</h1>

    <div id="cart-container">
        @include('cart_content', ['cart' => $cart, 'total' => $total])
    </div>

@if(count($cart) > 0)
    <div class="checkout-button-container">
        <a href="{{ route('checkout') }}" class="checkout-button">
            Tovább a fizetéshez
        </a>
    </div>
@endif



@endsection


