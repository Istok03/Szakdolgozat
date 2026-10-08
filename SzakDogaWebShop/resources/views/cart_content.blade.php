@if(count($cart) > 0)
    <table class="cart-table">
        <thead>
            <tr>
                <th>Kép</th>
                <th>Név</th>
                <th>Mennyiség</th>
                <th>Ár</th>
                <th>Művelet</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart as $id => $item)
                <tr data-id="{{ $id }}">
                    <td><img src="{{ asset($item['image'] ?? 'images/default.png') }}" width="80" alt="{{ $item['name'] }}"></td>
                    <td>{{ $item['name'] }}</td>
                    <td>
                        <button class="cart-action" data-id="{{ $id }}" data-action="decrease">−</button>
                        <span id="qty-{{ $id }}">{{ $item['quantity'] }}</span>
                        <button class="cart-action" data-id="{{ $id }}" data-action="increase">+</button>
                    </td>
                    <td>
                        {{ number_format($item['price'] * (1 - ($item['discount'] ?? 0) / 100) * $item['quantity'], 0, ',', ' ') }} Ft
                    </td>
                    <td>
                        <button class="cart-action" data-id="{{ $id }}" data-action="remove">❌ Törlés</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="cart-total">
        <h3>Összesen: <span id="cart-total">{{ number_format($total, 0, ',', ' ') }}</span> Ft</h3>
    </div>
@else
    <p class="empty-cart">A kosár üres.</p>
@endif
