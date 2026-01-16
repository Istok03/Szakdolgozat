<div class="category-dropdown-wrapper">
    <button class="category-toggle-btn" onclick="toggleCategoryDropdown()">
        Kategóriák <span class="arrow">▼</span>
    </button>

    <div id="categoryDropdown" class="category-dropdown hidden">
       @foreach($categories as $category)
            <a href="{{ route('products.byCategory', ['id' => $category->id]) }}" class="category-link">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>

<script>
function toggleCategoryDropdown() {
    const dropdown = document.getElementById('categoryDropdown');
    dropdown.classList.toggle('hidden');
}
</script>
