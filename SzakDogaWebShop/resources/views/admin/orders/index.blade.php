@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Rendelések listája</h1>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Felhasználó</th>
                <th>Státusz</th>
                <th>Összeg</th>
                <th>Létrehozva</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge 
                            @if($order->status === 'pending') bg-warning 
                            @elseif($order->status === 'paid') bg-success 
                            @elseif($order->status === 'shipped') bg-info 
                            @else bg-danger 
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($order->total_price, 0, ',', ' ') }} Ft</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                            Részletek
                        </a>
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-secondary">
                            Szerkesztés
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
