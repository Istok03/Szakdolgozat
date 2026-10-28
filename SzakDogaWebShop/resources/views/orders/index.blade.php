@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush

@section('content')
<div class="profile-page orders-page">
    <header class="profile-heading">
        <div><h1>Rendeléseim</h1><p>Itt követheted a rendeléseid állapotát és megtekintheted a részleteiket.</p></div>
        <a href="{{ route('dashboard') }}" class="profile-back">Vissza a fiókomhoz</a>
    </header>
    <div class="order-list">
        @forelse($orders as $order)
            <article class="profile-card">
                <div class="order-card-heading">
                    <h2>Rendelés #{{ $order->id }}</h2>
                    <span class="order-status">{{ \App\Models\Order::STATUSES[$order->status] ?? 'Ismeretlen állapot' }}</span>
                </div>
                <dl class="order-summary">
                    <div><dt>Leadás időpontja</dt><dd>{{ $order->created_at->format('Y-m-d H:i') }}</dd></div>
                    <div><dt>Termékek száma</dt><dd>{{ $order->items_sum_quantity ?? 0 }} db</dd></div>
                    <div><dt>Rendelés összege</dt><dd>{{ number_format($order->total, 0, ',', ' ') }} Ft</dd></div>
                </dl>
                <a href="{{ route('orders.show', $order) }}" class="profile-button" aria-label="A(z) #{{ $order->id }} rendelés részletei">Részletek megtekintése</a>
            </article>
        @empty
            <section class="profile-card">
                <h2>Még nincs rendelésed</h2>
                <p>A leadott rendeléseid itt fognak megjelenni.</p>
                <a href="{{ route('products') }}" class="profile-button">Termékek böngészése</a>
            </section>
        @endforelse
    </div>
    {{ $orders->links('partials.pagination') }}
</div>
@endsection
