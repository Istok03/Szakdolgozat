@extends('layouts.admin')

@section('content')
<div class="container py-4">

    <h1 class="fw-bold text-white mb-4 text-center">Termék szerkesztése</h1>

    <div class="card border-0 shadow mx-auto" style="background-color: #7c3aed; color: white; max-width: 960px;">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label text-white">Név</label>
                            <input type="text" name="name" id="name" class="form-control bg-light text-dark" value="{{ old('name', $product->name) }}">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label text-white">Ár</label>
                            <input type="number" name="price" id="price" class="form-control bg-light text-dark" value="{{ old('price', $product->price) }}">
                        </div>

                        <div class="mb-3">
                            <label for="discount" class="form-label text-white">Akciós százalék</label>
                            <input type="number" name="discount" id="discount" class="form-control bg-light text-dark" value="{{ old('discount', $product->discount) }}">
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label text-white">Új kép feltöltése (opcionális)</label>
                            <input type="file" name="image" id="image" class="form-control bg-light text-dark">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.products.index') }}" class="btn-admin-light">Vissza</a>
                            <button type="submit" class="btn-admin-light">Frissítés</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategória:</label>
                        <select name="category_id" id="category_id" class="form-control">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label text-white">Leírás</label>
                            <textarea name="description" id="description" rows="8" class="form-control bg-light text-dark">{{ old('description', $product->description) }}</textarea>
                        </div>

                        @if($product->image)
                            <div class="text-center">
                                <label class="form-label text-white">Jelenlegi kép:</label><br>
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Termék kép"
                                     style="max-width: 100%; height: auto; border-radius: 8px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            </div>
                        @endif
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
