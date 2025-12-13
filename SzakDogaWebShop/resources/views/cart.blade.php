@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush

@section('content')
    <h1>Kosár</h1>

    <div id="cart-container">
        @include('cart_content', ['cart' => $cart, 'total' => $total])
    </div>
@endsection
