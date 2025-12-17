
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Rendelés leadása</h2>

    <form action="{{ route('order.store') }}" method="POST">
        @csrf
        <div>
            <label>Név:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Telefonszám:</label>
            <input type="text" name="phone" required>
        </div>
        <div>
            <label>Cím:</label>
            <input type="text" name="address" required>
        </div>

        <h3>Kosár tartalma:</h3>
        <ul>
            @foreach($cart as $id => $item)
                <li>
                    {{ $item['name'] }} – {{ $item['quantity'] }} db – 
                    {{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100), 0, ',', ' ') }} Ft/db
                </li>
            @endforeach
        </ul>

        <p>Összesen: <strong>{{ number_format($total, 0, ',', ' ') }} Ft</strong></p>

        <button type="submit">Rendelés leadása</button>
    </form>
</div>
@endsection
