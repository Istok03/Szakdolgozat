<form id="filterPanel" class="filter-panel {{ request()->hasAny(['name', 'min', 'max', 'sale', 'category']) ? 'show' : '' }}" action="{{ request()->routeIs('offers') ? route('offers') : route('products') }}" method="GET">
    @if(request()->filled('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    <label for="filterName">Terméknév</label>
    <input id="filterName" name="name" type="search" maxlength="255" value="{{ request('name') }}" placeholder="Név alapján">
    <label for="filterMin">Minimum ár (Ft)</label>
    <input id="filterMin" name="min" type="number" min="0" step="0.01" value="{{ request('min') }}">
    <label for="filterMax">Maximum ár (Ft)</label>
    <input id="filterMax" name="max" type="number" min="0" step="0.01" value="{{ request('max') }}">
    @if(request()->routeIs('offers'))
        <input type="hidden" name="sale" value="1">
    @else
        <label><input id="filterSale" name="sale" type="checkbox" value="1" @checked(request('sale') == 1)> Csak akciós termékek</label>
    @endif
    <button id="applyFilter" type="submit" class="btn btn-primary">Szűrés alkalmazása</button>
    <a href="{{ request()->routeIs('offers') ? route('offers') : route('products') }}">Szűrők törlése</a>
</form>
