@extends('layouts.app')

@section('content')
    <div class="products-page">
        <h1>Termékeink</h1>
        <p>Válogass az informatikai eszközök széles kínálatából</p>

        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>
                    @if($product->discount)
                        <p class="old-price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                        <p class="sale-price">
                            {{ number_format($product->price * (1 - $product->discount/100), 0, ',', ' ') }} Ft
                        </p>
                        <span class="badge">-{{ $product->discount }}%</span>
                    @else
                        <p>Ár: {{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                    @endif
                    <button>Kosárba</button>
                </div>
            @endforeach
        </div>
    </div>
@endsection