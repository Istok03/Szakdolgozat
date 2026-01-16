@extends('admin.layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center text-purple">🛍️ Új termék létrehozása</h2>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="card p-4 shadow-sm bg-light">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-bold text-purple">Név</label>
            <input type="text" name="name" class="form-control" placeholder="Pl. Asus Gaming Laptop" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label fw-bold text-purple">Ár (Ft)</label>
                <input type="number" name="price" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="discount" class="form-label fw-bold text-purple">Akciós százalék (%)</label>
                <input type="number" name="discount" class="form-control" min="0" max="100">
            </div>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label fw-bold text-purple">Kép feltöltése</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label fw-bold text-purple">Kategória</label>
            <select name="category_id" class="form-select" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold text-purple">Leírás</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Pl. Erős gamer laptop RTX 3050-el..."></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Vissza</a>
            <button type="submit" class="btn btn-purple">✅ Létrehozás</button>
        </div>
    </form>
</div>
@endsection
