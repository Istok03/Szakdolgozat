@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="profile-page" lang="hu">
    <header class="profile-heading">
        <div>
            <h1>Fiókom áttekintése</h1>
            <p>Üdv, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('products') }}" class="profile-back">Vásárlás folytatása</a>
    </header>
    <div class="profile-grid">
        <section class="profile-card">
            <h2>Profilom</h2>
            <p>{{ auth()->user()->email }}</p>
            <p>Itt módosíthatod a személyes adataidat és a jelszavadat.</p>
            <a href="{{ route('profile.edit') }}" class="profile-button">Profil szerkesztése</a>
        </section>
        <section class="profile-card">
            <h2>Kosaram</h2>
            <p>{{ $cartQuantity ? $cartQuantity.' termék van a kosaradban.' : 'A kosarad jelenleg üres.' }}</p>
            <a href="{{ route('cart.index') }}" class="profile-button">Kosár megtekintése</a>
        </section>
        <section class="profile-card">
            <h2>Rendeléseim összesítése</h2>
            @if($latestOrder)
                <p>Eddigi rendelések száma: {{ $orderCount }}</p>
                <p>Legutóbbi rendelés: #{{ $latestOrder->id }} · {{ $latestOrder->created_at->format('Y-m-d') }}</p>
                <p>Állapot: {{ \App\Models\Order::STATUSES[$latestOrder->status] ?? $latestOrder->status }}</p>
                <p>Összeg: {{ number_format($latestOrder->total, 0, ',', ' ') }} Ft</p>
                <a href="{{ route('orders.show', $latestOrder) }}" class="profile-button profile-button-secondary">Legutóbbi rendelés részletei</a>
            @else
                <p>Még nem adtál le rendelést.</p>
            @endif
            <div class="profile-actions"><a href="{{ route('orders.index') }}" class="profile-button">Összes rendelésem</a></div>
        </section>
        <section class="profile-card">
            <h2>Segítségre van szükséged?</h2>
            <p>A rendeléseddel kapcsolatos kérdésnél add meg a rendelés azonosítóját is.</p>
            <a href="{{ route('contact') }}" class="profile-button">Kapcsolatfelvétel</a>
        </section>
    </div>
</div>
@endsection
