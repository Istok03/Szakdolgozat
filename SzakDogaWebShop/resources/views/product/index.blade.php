@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-white mb-4">
            @isset($category)
                Termékek a(z) <strong>{{ $category->name }}</strong> kategóriában
            @else
                Minden termék
            @endisset
        </h2>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-4">
                    <div class="card bg-purple text-white">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ $product->description }}</p>
                            <p class="card-text fw-bold">{{ number_format($product->price, 0, ',', ' ') }} Ft</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-white">Nincs elérhető termék ebben a kategóriában.</p>
            @endforelse
        </div>
    </div>
@endsection
