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
    @if($randomReviews->count() > 0)
    <div class="home-reviews">
        <h2>Vásárlóink véleményei</h2>

        @foreach($randomReviews as $review)
            <div class="home-review-card">
                <div class="home-review-header">
                    <strong>{{ $review->user->name }}</strong>

                    <span class="review-stars">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $review->rating ? '★' : '☆' }}
                        @endfor
                    </span>
                </div>

                <p class="home-review-text">
                    "{{ Str::limit($review->comment, 120) }}"
                </p>

                <a href="{{ route('product.show', $review->product) }}" class="home-review-product">
                    {{ $review->product->name }}
                </a>
            </div>
        @endforeach
    </div>
@endif


@endsection