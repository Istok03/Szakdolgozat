@extends('layouts.admin')

<h1>Rendelések kezelése</h1>

<table>
    <thead>
        <tr>
            <th>Rendelés ID</th>
            <th>Név</th>
            <th>Email</th>
            <th>Összeg</th>
            <th>Státusz</th>
            <th>Dátum</th>
            <th>Művelet</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
       <tr>
            <td><img src="{{ asset($product->image) }}" width="60"></td>
            <td>{{ $product->name }}</td>
            <td>{{ number_format($product->price, 0, ',', ' ') }} Ft</td>
            <td>{{ $product->discount }}%</td>
            <td>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">Szerkesztés</a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Törlés</button>
                </form>
            </td>
        </tr>

        @endforeach
    </tbody>
</table>
