<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function mergeGuestCart(User $user, array $cart): void
    {
        DB::transaction(function () use ($user, $cart) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            foreach ($cart as $productId => $item) {
                $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
                $product = Product::find($productId);
                if (!$quantity || $quantity < 1 || !$product) {
                    continue;
                }

                $existing = CartItem::firstOrNew([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                $requested = ($existing->exists ? $existing->quantity : 0) + $quantity;
                $existing->quantity = min($requested, $product->stock);
                if ($existing->quantity < $requested) {
                    session()->flash('error', 'A kosár mennyiségeit az elérhető készlethez igazítottuk. Ellenőrizd a kosaradat.');
                }
                if ($existing->quantity === 0) {
                    if ($existing->exists) $existing->delete();
                    continue;
                }
                $existing->save();
            }
        });
    }
}
