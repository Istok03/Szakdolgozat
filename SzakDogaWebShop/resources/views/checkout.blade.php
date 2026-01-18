@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <h2>Rendelés leadása</h2>

    <form action="{{ route('order.store') }}" method="POST" class="checkout-form">
        @csrf
        <div class="form-group">
            <label for="name">Név:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Telefonszám:</label>
            <input type="text" name="phone" id="phone" required>
        </div>
        <div class="form-group">
            <label for="address">Cím:</label>
            <input type="text" name="address" id="address" required>
        </div>

        <h3>Kosár tartalma</h3>

        @if(count($cart) > 0)
            <table class="checkout-cart">
                <thead>
                    <tr>
                        <th>Termék</th>
                        <th>Mennyiség</th>
                        <th>Egységár</th>
                        <th>Összesen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }} db</td>
                            <td>{{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100), 0, ',', ' ') }} Ft</td>
                            <td>{{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100) * $item['quantity'], 0, ',', ' ') }} Ft</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="checkout-total">
                <strong>Összesen: {{ number_format($total, 0, ',', ' ') }} Ft</strong>
            </div>
        @else
            <p class="empty-cart">A kosár üres.</p>
        @endif

        <h3>Fizetési mód</h3>

<div class="payment-methods">
    <label>
        <input type="radio" name="payment_method" value="card" checked>
        Bankkártya
    </label>

    <label>
        <input type="radio" name="payment_method" value="paypal">
        PayPal
    </label>

    <label>
        <input type="radio" name="payment_method" value="cod">
        Utánvét
    </label>
</div>

<div id="card-form" class="card-form">
    <label>Kártyaszám</label>
    <input type="text" name="card_number" placeholder="1234 5678 9012 3456">

    <label>Lejárat</label>
    <input type="text" name="card_exp" placeholder="MM/YY">

    <label>CVC</label>
    <input type="text" name="card_cvc" placeholder="123">
</div>


        <button type="submit" class="checkout-submit">Rendelés leadása</button>
    </form>
</div>
@endsection
