@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4">Rendelések listája</h1>

    <div class="admin-table-card">

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Felhasználó</th>
                    <th>Státusz</th>
                    <th>Összeg</th>
                    <th>Létrehozva</th>
                    <th class="text-end">Műveletek</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->name }}</td>
                        <td>
                            <span class="badge-discount">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>{{ number_format($order->total, 0, ',', ' ') }} Ft</td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-admin-light">Részletek</a>
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn-admin-light">Szerkesztés</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>
@endsection
