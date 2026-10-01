@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Ár: {{ number_format($product->salePrice(), 0, ',', ' ') }} Ft</p>
    <a href="{{ route('admin.products.edit', $product) }}">Szerkesztés</a>
    <a href="{{ route('admin.products.index') }}">Vissza a termékekhez</a>
</div>
@endsection