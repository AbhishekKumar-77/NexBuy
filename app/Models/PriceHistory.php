<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = ['product_id', 'platform', 'price', 'recorded_date'];

    protected $casts = ['recorded_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
