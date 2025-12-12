@extends('layouts.app')

@section('content')
<div class="cart-container">
    <h1>Kosár</h1>

    @if(count($cart) > 0)
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Kép</th>
                    <th>Termék</th>
                    <th>Mennyiség</th>
                    <th>Ár</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                    <tr>
                        <td><img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="cart-img"></td>
                        <td>{{ $item['name'] }}</td>
                        <td>
                            <form action="{{ route('cart.decrease', $id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="qty-btn">−</button>
                            </form>

                            {{ $item['quantity'] }}

                            <form action="{{ route('cart.increase', $id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="qty-btn">+</button>
                            </form>
                        </td>

                        <td>{{ number_format($item['price'], 0, ',', ' ') }} Ft</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                <button type="submit" class="remove-btn">❌</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @if(count($cart) > 0)
        <div class="cart-total">
            <h3>Összesen: {{ number_format($total, 0, ',', ' ') }} Ft</h3>
        </div>
    @endif

    @else
        <p class="empty-cart">A kosár üres.</p>
    @endif
</div>
@endsection
