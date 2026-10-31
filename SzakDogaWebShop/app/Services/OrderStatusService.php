<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    public function update(Order $order, array $attributes): void
    {
        DB::transaction(function () use ($order, $attributes) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $reserve = $attributes['status'] !== 'cancelled';
            if ($locked->manages_stock && (bool) $locked->stock_reserved !== $reserve) {
                $items = $locked->items()->get();
                $products = Product::whereIn('id', $items->pluck('product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                foreach ($items->groupBy('product_id') as $productId => $lines) {
                    $product = $products->get($productId);
                    $quantity = $lines->sum('quantity');
                    if ($reserve && (!$product || $product->stock < $quantity)) {
                        throw ValidationException::withMessages(['status' => 'A rendelés nem aktiválható újra: egy termék hiányzik vagy nincs elegendő készleten.']);
                    }
                    if ($product) {
                        $reserve ? $product->decrement('stock', $quantity) : $product->increment('stock', $quantity);
                    }
                }
                $locked->stock_reserved = $reserve;
            }
            $locked->fill($attributes)->save();
        });
    }
}
