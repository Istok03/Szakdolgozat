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

       <button class="cart-btn" data-id="{{ $product->id }}">Kosárba</button>

    </div>
</div>
<div class="reviews-section" style="margin-top: 50px;">

    <h2>Vélemények</h2>

    {{-- Átlagos értékelés --}}
    @if($product->reviews->count() > 0)
        <p>
            Átlagos értékelés:
            <strong>{{ number_format($product->reviews->avg('rating'), 1) }}</strong> / 5
            ({{ $product->reviews->count() }} vélemény)
        </p>
    @else
        <p>Még nincsenek vélemények.</p>
    @endif

    <hr>

    {{-- Vélemény írása --}}
    @auth
        @php
            $existingReview = $product->reviews->where('user_id', auth()->id())->first();
        @endphp

        <h3>Vélemény írása</h3>

        <form action="{{ route('reviews.store', $product) }}" method="POST" style="margin-bottom: 30px;">
            @csrf

            <label for="rating">Értékelés (1–5):</label>
            <select name="rating" id="rating" class="form-select" style="width: 120px; margin-bottom: 10px;">
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}"
                        @if(isset($existingReview) && $existingReview->rating == $i) selected @endif>
                        {{ $i }} csillag
                    </option>
                @endfor
            </select>

            <label for="comment">Vélemény:</label>
            <textarea name="comment" id="comment" class="review-form-textarea" rows="3">
                {{ $existingReview->comment ?? '' }}
            </textarea>

            <button type="submit" class="btn btn-primary review-submit-btn">
                {{ $existingReview ? 'Vélemény frissítése' : 'Vélemény elküldése' }}
            </button>
        </form>
    @else
        <p>A vélemény írásához <a href="/login">jelentkezz be</a>.</p>
    @endauth

    <hr>

    @foreach($product->reviews as $review)
        <div style="margin-bottom: 20px;">
            <strong>{{ $review->user->name }}</strong>

            <span style="color: #f1c40f; font-size: 18px;">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </span>

            <p>{{ $review->comment }}</p>
            <small style="color: gray;">{{ $review->created_at->format('Y.m.d H:i') }}</small>

            @if(auth()->check() && (auth()->id() === $review->user_id || auth()->user()->is_admin))
                <form action="{{ route('reviews.destroy', $review) }}" method="POST" style="margin-top: 5px;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger review-delete-btn">Törlés</button>
                </form>
            @endif
        </div>

        <hr>
    @endforeach

</div>

@endsection

