<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['session_id', 'product_id', 'alert_price', 'preferred_platform'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
