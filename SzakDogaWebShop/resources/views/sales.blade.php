@extends('layouts.app')

@section('content')
    <div class="sales-page">
        <h1>Akciós termékeink</h1>
        <p>Csak a kedvezményes ajánlatok, ne maradj le!</p>
 <div class="filter-toggle">
        <button id="filterButton" class="filter-btn">🔍 Szűrési lehetőségek</button>
    </div>

    <div id="filterPanel" class="filter-panel">
        <form method="GET" action="{{ route('products') }}">
            <div class="filter-group">
                <label for="search">🔎 Név alapján:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label for="min_price">💰 Minimum ár:</label>
                <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}">
            </div>

            <div class="filter-group">
                <label for="max_price">💰 Maximum ár:</label>
                <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}">
            </div>

            <div class="filter-group checkbox-group">
                <label>
                    <input type="checkbox" name="discount_only" {{ request('discount_only') ? 'checked' : '' }}>
                    Csak akciós termékek
                </label>
            </div>

        <button type="submit" class="apply-btn">✅ Szűrés alkalmazása</button>
    </form>
</div>

        <div class="product-grid">
            @forelse($products as $product)
                <div class="product-card">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                    <h3>{{ $product->name }}</h3>

                    @if($product->description)
                        <p class="description">{{ Str::limit($product->description, 60) }}</p>
                    @endif

                    <p class="old-price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                    <p class="sale-price">
                        {{ number_format($product->price * (1 - $product->discount/100), 0, ',', ' ') }} Ft
                    </p>
                    <span class="badge">-{{ $product->discount }}%</span>

                    <button class="cart-btn">Kosárba</button>
                </div>
            @empty
                <p>Nincs jelenleg akciós termék.</p>
            @endforelse
        </div>
    </div>
@endsection
