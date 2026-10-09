@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4 text-center">Rendelés #{{ $order->id }} részletei</h1>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card border-0 shadow" style="background-color: #7c3aed; color: white;">
                <div class="card-body">
                    <h5 class="card-title text-uppercase mb-3">Felhasználó</h5>
                    <p><strong>Név:</strong> {{ $order->name }}</p>
                    <p><strong>Email:</strong> {{ $order->email }}</p>
                    <p><strong>Telefon:</strong> {{ $order->phone }}</p>
                    <p><strong>Cím:</strong> {{ $order->address }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow" style="background-color: #7c3aed; color: white;">
                <div class="card-body">
                    <h5 class="card-title text-uppercase mb-3">Rendelés adatai</h5>
                    <p><strong>Státusz:</strong> <span class="badge-discount">{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span></p>
                    <p><strong>Fizetés:</strong> {{ $order->payment_method === 'cod' ? 'Utánvét' : 'Nincs rögzítve' }}</p>
                    <p><strong>Összeg:</strong> {{ number_format($order->total, 0, ',', ' ') }} Ft</p>
                    <p><strong>Létrehozva:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>

                    @foreach($order->items as $item)
                        <hr class="my-3 border-white">
                        <p><strong>Termék:</strong> {{ $item->product->name ?? 'Törölt termék' }}</p>
                        <p><strong>Mennyiség:</strong> {{ $item->quantity }}</p>
                        <p><strong>Egységár:</strong> {{ number_format($item->price, 0, ',', ' ') }} Ft</p>
                        <p><strong>Részösszeg:</strong> {{ number_format($item->price * $item->quantity, 0, ',', ' ') }} Ft</p>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 text-end mt-3">
            <a href="{{ route('admin.orders.index') }}" class="btn-admin-light">Vissza a listához</a>
        </div>

    </div>

</div>
@endsection
