@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Rendelés #{{ $order->id }} részletei</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Felhasználó</h5>
            <p><strong>Név:</strong> {{ $order->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Rendelés adatai</h5>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mt-2">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="form-label">Státusz módosítása:</label>
                    <select name="status" id="status" onchange="this.form.submit()" class="form-select form-select-sm w-auto d-inline-block">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>

            <p><strong>Összeg:</strong> {{ number_format($order->total_price, 0, ',', ' ') }} Ft</p>
            <p><strong>Létrehozva:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <h3>Rendelési tételek</h3>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Termék</th>
                <th>Mennyiség</th>
                <th>Egységár</th>
                <th>Összesen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0, ',', ' ') }} Ft</td>
                    <td>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} Ft</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Vissza a listához</a>
</div>
@endsection
