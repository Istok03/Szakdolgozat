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
            <label for="address">Vásárlói cím (házhoz szállításnál szállítási cím):</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}" maxlength="255" required>
        </div>

        <fieldset class="shipping-methods">
            <legend>Szállítási mód</legend>
            @foreach($shippingMethods as $key => $method)
                <label class="shipping-option">
                    <input type="radio" name="shipping_method" value="{{ $key }}" data-fee="{{ $method['fee'] }}" @checked($selectedShipping === $key) required>
                    <span>{{ $method['label'] }} — {{ number_format($method['fee'], 0, ',', ' ') }} Ft
                        @if($key === 'pickup')<small>Átvételi cím: {{ config('shipping.pickup_address') }}</small>@endif
                    </span>
                </label>
            @endforeach
        </fieldset>

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
                            <td>{{ $item['name'] }}
                                @if($item['quantity'] > $item['stock'])
                                    <p class="stock-warning">Csak {{ $item['stock'] }} db érhető el. Módosítsd a kosarat.</p>
                                @endif
                            </td>
                            <td data-label="Mennyiség">{{ $item['quantity'] }} db</td>
                            <td data-label="Egységár">{{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100), 0, ',', ' ') }} Ft</td>
                            <td data-label="Összesen">{{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100) * $item['quantity'], 0, ',', ' ') }} Ft</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="checkout-total">
                <p>Termékek összege: {{ number_format($total, 0, ',', ' ') }} Ft</p>
                <p>Szállítás: <span id="shipping-fee">{{ number_format($shippingMethods[$selectedShipping]['fee'], 0, ',', ' ') }}</span> Ft</p>
                <strong aria-live="polite">Fizetendő: <span id="checkout-grand-total" data-subtotal="{{ $total }}">{{ number_format($total + $shippingMethods[$selectedShipping]['fee'], 0, ',', ' ') }}</span> Ft</strong>
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

        <p>A kosárba helyezés még nem foglalja le a készletet. A készletet rendeléskor ellenőrizzük.</p>
        <p><a href="{{ route('cart.index') }}">Vissza a kosárhoz</a></p>
        <button type="submit" class="checkout-submit" @disabled(count($cart) === 0 || collect($cart)->contains(fn ($item) => $item['quantity'] > $item['stock']))>Rendelés leadása</button>
    </form>
</div>
<script src="{{ asset('js/checkout.js') }}" defer></script>
@endsection
