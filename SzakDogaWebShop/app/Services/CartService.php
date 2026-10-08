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
                if (!$quantity || $quantity < 1 || !Product::whereKey($productId)->exists()) {
                    continue;
                }

                $existing = CartItem::firstOrNew([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                $existing->quantity = ($existing->exists ? $existing->quantity : 0) + $quantity;
                $existing->save();
            }
        });
    }
}
