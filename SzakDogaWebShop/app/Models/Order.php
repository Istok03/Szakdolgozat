<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    public const STATUSES = [
        'pending' => 'Függőben',
        'processing' => 'Feldolgozás alatt',
        'paid' => 'Kifizetve',
        'shipped' => 'Feladva',
        'completed' => 'Teljesítve',
        'cancelled' => 'Törölve',
    ];
    protected $fillable =[
        'user_id',
        'status',
        'total',
        'name',
        'email',
        'phone',
        'address',
        'payment_method',
    ];


    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany{
        return $this->hasMany(OrderItem::class);
    }
}
