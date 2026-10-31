<p class="stock-status {{ $product->stock > 0 ? '' : 'stock-warning' }}">{{ $product->stock > 0 ? 'Raktáron: '.$product->stock.' db' : 'Jelenleg nincs készleten' }}</p>
<button class="cart-btn" data-id="{{ $product->id }}" @disabled($product->stock < 1)>{{ $product->stock > 0 ? 'Kosárba' : 'Elfogyott' }}</button>
