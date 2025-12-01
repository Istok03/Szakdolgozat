@extends('layouts.app')

@section('content')
    <div class="sales-page">
        <h1>Akciós ajánlatok</h1>
        <p>Ne hagyd ki a legjobb kedvezményeket!</p>

        <div class="sales-grid">
            @foreach($sales as $item)
                <div class="sale-card">
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
                    <h3>{{ $item['name'] }}</h3>
                    <p class="old-price">{{ number_format($item['old_price'], 0, ',', ' ') }} Ft</p>
                    <p class="sale-price">{{ number_format($item['sale_price'], 0, ',', ' ') }} Ft</p>
                    <span class="badge">-{{ $item['discount'] }}%</span>
                    <button>Kosárba</button>
                </div>
            @endforeach
        </div>
    </div>
@endsection
