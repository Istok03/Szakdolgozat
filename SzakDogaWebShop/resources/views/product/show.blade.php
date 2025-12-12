@extends('layouts.app')

@section('content')
    <div class="product-details">
    <div class="product-image">
        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
    </div>

    <div class="product-info">
        <h1>{{ $product->name }}</h1>
        <p class="description">{{ $product->description }}</p>

        @if($product->discount > 0)
            <p class="old-price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
            <p class="sale-price">
                Akciós ár: {{ number_format($product->price * (1 - $product->discount / 100), 0, ',', ' ') }} Ft
            </p>
            <span class="badge">-{{ $product->discount }}%</span>
        @else
            <p class="price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
        @endif

        <form action="{{ route('cart.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit" class="cart-btn">Kosárba</button>
        </form>
    </div>
</div>
@endsection