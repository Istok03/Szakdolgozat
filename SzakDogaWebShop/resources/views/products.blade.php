@extends('layouts.app')

@section('content')
    <div class="products-page">
        <h1>Termékeink</h1>
        <p class="subtitle">Modern informatikai eszközök, akciós ajánlatokkal</p>

        <div class="filter-toggle">
                    <button id="filterToggle" class="btn btn-success">Szűrő</button>

        <div id="filterPanel" class="filter-panel">
            <input id="filterName" type="text" placeholder="Név alapján">
            <input id="filterMin" type="number" placeholder="Minimum ár">
            <input id="filterMax" type="number" placeholder="Maximum ár">
            <label>
                <input id="filterSale" type="checkbox"> Csak akciós termékek
            </label>
            <button id="applyFilter" class="btn btn-primary">Szűrés alkalmazása</button>
        </div>

        </div>

 
        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                         <a href="{{ route('product.show', $product->id) }}">
                            <img src="{{ asset($product->image) }}">
                            <h3>{{ $product->name }}</h3>
                        </a>

                        @if($product->discount > 0)
                            <p class="old-price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                            <p class="sale-price">{{ number_format($product->price * (1 - $product->discount / 100), 0, ',', ' ') }} Ft</p>
                            <span class="badge">-{{ $product->discount }}%</span>
                        @else
                            <p class="price">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                        @endif

                   <button class="cart-btn" data-id="{{ $product->id }}">Kosárba</button>
                </div>
            @endforeach
        </div>
    </div>
@endsection