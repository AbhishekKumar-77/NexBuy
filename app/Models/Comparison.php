<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    protected $fillable = ['session_id', 'title', 'product_ids', 'tco_inputs'];

    protected $casts = ['product_ids' => 'array', 'tco_inputs' => 'array'];
}
