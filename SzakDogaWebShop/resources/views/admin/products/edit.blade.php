@extends('layouts.admin')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow w-100" style="background-color: #dee2ff; max-width: 960px;">
        <div class="card-body">
            <h2 class="fw-bold text-center mb-4">🛠️ Termék szerkesztése</h2>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-6">
                        @foreach ([
                            'name' => 'Név',
                            'price' => 'Ár (Ft)',
                            'discount' => 'Akció (%)',
                            'image' => 'Új kép',
                            'category_id' => 'Kategória'
                        ] as $field => $label)
                            <div class="row mb-3 align-items-center">
                                <label for="{{ $field }}" class="col-md-4 col-form-label text-md-end fw-semibold">{{ $label }}</label>
                                <div class="col-md-8">
                                    @if($field === 'image')
                                        <input type="file" name="image" id="image" class="form-control bg-light text-dark">
                                    @elseif($field === 'category_id')
                                        <select name="category_id" id="category_id" class="form-select bg-light text-dark">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $field === 'discount' ? 'number' : 'text' }}" name="{{ $field }}" id="{{ $field }}"
                                               class="form-control bg-light text-dark"
                                               value="{{ old($field, $product->$field) }}">
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark px-4">Vissza</a>
                            <button type="submit" class="btn btn-dark px-4">Frissítés</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Leírás</label>
                            <textarea name="description" id="description" rows="8" class="form-control bg-light text-dark">{{ old('description', $product->description) }}</textarea>
                        </div>

                        @if($product->image)
                            <div class="text-center mt-3">
                                <label class="form-label fw-semibold d-block mb-2">Jelenlegi kép:</label>
                                <img src="{{ asset($product->image) }}" alt="Termék kép"
                                     style="max-width: 320px; width: 100%; height: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
