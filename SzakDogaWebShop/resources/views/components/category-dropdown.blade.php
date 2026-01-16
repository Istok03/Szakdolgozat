<div class="navbar-categories">
    <button class="category-toggle-btn" onclick="toggleCategoryDropdown()">
        Kategóriák <span class="arrow">▼</span>
    </button>

    <div id="categoryDropdown" class="category-dropdown">
        @foreach($categories as $category)
            <a href="{{ url('/products?category=' . $category->id) }}" class="category-link">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>
