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
                        <td>{{ $item['quantity'] }}</td>
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
    @else
        <p class="empty-cart">A kosár üres.</p>
    @endif
</div>
@endsection
