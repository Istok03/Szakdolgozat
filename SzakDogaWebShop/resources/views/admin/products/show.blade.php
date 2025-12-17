@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Rendelés adatai</h3>
    <p><strong>Név:</strong> {{ $order->name }}</p>
    <p><strong>Email:</strong> {{ $order->email }}</p>
    <p><strong>Cím:</strong> {{ $order->address }}</p>
    <p><strong>Státusz:</strong> {{ $order->status }}</p>

    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
        @csrf
        @method('PATCH')
        <label for="status">Státusz módosítása:</label>
        <select name="status" id="status">
            <option value="Feldolgozás alatt" {{ $order->status == 'Feldolgozás alatt' ? 'selected' : '' }}>Feldolgozás alatt</option>
            <option value="Kiszállítva" {{ $order->status == 'Kiszállítva' ? 'selected' : '' }}>Kiszállítva</option>
            <option value="Teljesítve" {{ $order->status == 'Teljesítve' ? 'selected' : '' }}>Teljesítve</option>
        </select>
        <button type="submit">Státusz frissítése</button>
    </form>

    <h4>Termékek:</h4>
    <table>
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
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }} db</td>
                <td>{{ number_format($item->price, 0, ',', ' ') }} Ft</td>
                <td>{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} Ft</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Teljes összeg:</strong> {{ number_format($order->total_price, 0, ',', ' ') }} Ft</p>

    <a href="{{ route('admin.orders.index') }}">← Vissza a rendeléslistához</a>
</div>
@endsection
