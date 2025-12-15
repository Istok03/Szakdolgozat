@extends('layouts.admin')
<h1>Termék részletei</h1>


<p><strong>Név:</strong> {{ $product->name }}</p>
<p><strong>Ár:</strong> {{ number_format($product->price, 0, ',', ' ') }} Ft</p>
<p><strong>Kedvezmény:</strong> 
    @if($product->discount)
        {{ $product->discount }} %
    @else
        Nincs
    @endif
</p>
<p><strong>Leírás:</strong> {{ $product->description ?? 'Nincs leírás' }}</p>

@if($product->image)
    <p><strong>Kép:</strong></p>
    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="200">
@else
    <p><strong>Kép:</strong> Nincs kép</p>
@endif

<hr>

<a href="{{ route('admin.products.edit', $product->id) }}">Szerkesztés</a> |
<a href="{{ route('admin.products.index') }}">Vissza a listához</a>
