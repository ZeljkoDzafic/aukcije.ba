<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BidIncrement extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['price_from', 'price_to', 'increment'];

    protected $casts = [
        'price_from' => 'float',
        'price_to'   => 'float',
        'increment'  => 'float',
    ];

    public static function getIncrement(float $currentPrice): float
    {
        $row = static::where('price_from', '<=', $currentPrice)
            ->where(function ($q) use ($currentPrice) {
                $q->whereNull('price_to')->orWhere('price_to', '>', $currentPrice);
            })
            ->orderByDesc('price_from')
            ->first();

        return $row ? $row->increment : 50.00;
    }
}
