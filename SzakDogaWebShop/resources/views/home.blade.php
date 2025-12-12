@extends('layouts.app')

@section('content')
    <div class="home-page">
        <h1>Üdvözöljük Istók informatikai webboltjában</h1>
        <p>Fedezd fel a legjobb ajánlatokat</p>
        <a href="{{ route('offers') }}" class="cta-button">Akciók megtekintése</a>
    </div>

    <div class="featured-products">
        <h2>Kiemelt termékek</h2>
        <div class="product-grid">
            @forelse($products as $product)
                <div class="product-card">
                      <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset($product->image) }}">
                            <h3>{{ $product->name }}</h3>
                        </a>

                    @if($product->discount > 0)
                        <p class="old-price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                        <p class="sale-price">
                            Akciós ár: {{ number_format($product->price * (1 - $product->discount / 100), 0, ',', ' ') }} Ft
                        </p>
                        <span class="badge">-{{ $product->discount }}%</span>
                    @else
                        <p class="price">Ár: {{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                    @endif

                  <button class="cart-btn" data-id="{{ $product->id }}">Kosárba</button>
                </div>
            @empty
                <p>Nincs kiemelt termék jelenleg.</p>
            @endforelse
        </div>
    </div>
@endsection