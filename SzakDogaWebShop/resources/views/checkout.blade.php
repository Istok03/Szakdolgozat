@extends('layouts.app')

@section('content')
<div class="checkout-container">
    <h2>Rendelés leadása</h2>

    <form action="{{ route('order.store') }}" method="POST" class="checkout-form">
        @csrf
        <div class="form-group">
            <label for="name">Név:</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" maxlength="255" required>
        </div>
        <div class="form-group">
            <label for="phone">Telefonszám:</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" maxlength="30" required>
        </div>
        <div class="form-group">
            <label for="address">Cím:</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" maxlength="255" required>
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
        <input type="radio" name="payment_method" value="cod" checked>
        Utánvét
    </label>
</div>

        <button type="submit" class="checkout-submit" @disabled(count($cart) === 0)>Rendelés leadása</button>
    </form>
</div>
@endsection
