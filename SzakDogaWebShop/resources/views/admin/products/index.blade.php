@extends('layouts.admin')
<h1>Termékek kezelése</h1>


<table>
    <thead>
        <tr>
            <th>Név</th>
            <th>Ár</th>
            <th>Akciós ár</th>
            <th>Műveletek</th>
            <th>Kép</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ number_format($product->price, 0, ',', ' ') }} Ft</td>
            <td>
                @if($product->discount_price)
                    {{ number_format($product->discount_price, 0, ',', ' ') }} Ft
                @else
                    —
                @endif
            </td>
            <td>
                <a href="{{ route('admin.products.edit', $product->id) }}">Szerkesztés</a> |
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Törlés</button>
                </form>
            </td>
            <td>
                @if($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="100">
                @else
                    Nincs kép
                @endif
            </td>


        </tr>
        @endforeach
    </tbody>
</table>
