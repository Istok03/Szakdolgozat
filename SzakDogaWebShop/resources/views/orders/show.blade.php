@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@section('content')
<div class="profile-page orders-page">
    <header class="profile-heading">
        <div>
            <h1>{{ $confirmed ? 'Köszönjük a rendelésed!' : 'Rendelés #'.$order->id }}</h1>
            <p>{{ $confirmed ? 'A rendelésedet sikeresen rögzítettük. Az alábbiakban ellenőrizheted az adatait.' : 'A rendelés leadásakor rögzített adatok és az aktuális állapot.' }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="profile-back">Összes rendelésem</a>
    </header>
    <div class="profile-grid">
        <section class="profile-card">
            <h2>Rendelés adatai</h2>
            <dl class="order-data">
                <dt>Azonosító</dt><dd>#{{ $order->id }}</dd>
                <dt>Leadás időpontja</dt><dd>{{ $order->created_at->format('Y-m-d H:i') }}</dd>
                <dt>Állapot</dt><dd><span class="order-status">{{ \App\Models\Order::STATUSES[$order->status] ?? 'Ismeretlen állapot' }}</span></dd>
                <dt>Fizetési mód</dt><dd>{{ $order->payment_method === 'cod' ? 'Utánvét' : 'Nincs rögzítve' }}</dd>
            </dl>
            @if($confirmed && $order->payment_method === 'cod')
                <p>A fizetés a csomag átvételekor történik.</p>
            @endif
        </section>
        <section class="profile-card">
            <h2>Vásárlói és szállítási adatok</h2>
            <dl class="order-data">
                <dt>Név</dt><dd>{{ $order->name ?: 'Nincs rögzítve' }}</dd>
                <dt>E-mail-cím</dt><dd>{{ $order->email ?: 'Nincs rögzítve' }}</dd>
                <dt>Telefonszám</dt><dd>{{ $order->phone ?: 'Nincs rögzítve' }}</dd>
                <dt>Szállítási cím</dt><dd>{{ $order->address ?: 'Nincs rögzítve' }}</dd>
            </dl>
        </section>
    </div>
    <section class="profile-card order-items">
        <h2>Rendelt termékek</h2>
        <ul class="order-item-list">
            @forelse($order->items as $item)
                <li>
                    <div class="order-item-name">
                        <h3>{{ $item->product_name ?? $item->product?->name ?? 'Törölt termék' }}</h3>
                        @if($item->product)
                            <a href="{{ route('product.show', $item->product_id) }}">Jelenlegi termékoldal</a>
                        @else
                            <span>A termék már nem érhető el az áruházban.</span>
                        @endif
                    </div>
                    <dl class="order-summary">
                        <div><dt>Mennyiség</dt><dd>{{ $item->quantity }} db</dd></div>
                        <div><dt>Rendeléskori egységár</dt><dd>{{ number_format($item->price, 0, ',', ' ') }} Ft</dd></div>
                        <div><dt>Részösszeg</dt><dd>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} Ft</dd></div>
                    </dl>
                </li>
            @empty
                <li>Ehhez a korábbi rendeléshez nem áll rendelkezésre terméklista.</li>
            @endforelse
        </ul>
        <p class="order-total">Rendelés összege: <strong>{{ number_format($order->total, 0, ',', ' ') }} Ft</strong></p>
    </section>
    <div class="profile-actions">
        <a href="{{ route('products') }}" class="profile-button">Vásárlás folytatása</a>
        <a href="{{ route('contact') }}" class="profile-back">Kérdésem van a rendeléssel kapcsolatban</a>
    </div>
    <p class="order-help">Kapcsolatfelvételkor hivatkozz a(z) #{{ $order->id }} rendelési azonosítóra.</p>
</div>
@endsection
