@extends('layouts.admin')

<h1>Rendelések kezelése</h1>

<table>
    <thead>
        <tr>
            <th>Rendelés ID</th>
            <th>Név</th>
            <th>Email</th>
            <th>Összeg</th>
            <th>Státusz</th>
            <th>Dátum</th>
            <th>Művelet</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->name }}</td>
            <td>{{ $order->email }}</td>
            <td>{{ number_format($order->total_price, 0, ',', ' ') }} Ft</td>
            <td>{{ $order->status }}</td>
            <td>{{ $order->created_at->format('Y.m.d H:i') }}</td>
            <td>
                <a href="{{ route('admin.orders.show', $order->id) }}">Megtekintés</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
