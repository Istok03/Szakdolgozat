<h1>Termék szerkesztése</h1>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label for="name">Név:</label>
        <input type="text" id="name" name="name" value="{{ $product->name }}" required>
    </div>

    <div>
        <label for="price">Ár:</label>
        <input type="number" id="price" name="price" value="{{ $product->price }}" required>
    </div>

    <div>
        <label for="discount">Akciós százalék:</label>
        <input type="number" id="discount" name="discount" value="{{ $product->discount }}">
    </div>

    <div>
        <label for="description">Leírás:</label>
        <textarea id="description" name="description">{{ $product->description }}</textarea>
    </div>

    <div>
        <label for="image">Új kép feltöltése (opcionális):</label>
        <input type="file" id="image" name="image" accept="image/*">
    </div>

    @if($product->image)
        <p>Jelenlegi kép:</p>
        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="120">
    @endif

    <button type="submit">Frissítés</button>
</form>
