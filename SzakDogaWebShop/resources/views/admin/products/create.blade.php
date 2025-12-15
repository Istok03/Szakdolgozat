<h1>Új termék hozzáadása</h1>

<form action="{{ route('admin.products.store') }}" method="POST">
    @csrf
    <div>
        <label for="name">Név:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div>
        <label for="price">Ár:</label>
        <input type="number" id="price" name="price" required>
    </div>

    <div>
        <label for="discount_price">Akciós ár (opcionális):</label>
        <input type="number" id="discount_price" name="discount_price">
    </div>

    <div>
        <label for="description">Leírás:</label>
        <textarea id="description" name="description"></textarea>
    </div>

    <button type="submit">Mentés</button>
</form>
