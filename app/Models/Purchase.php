<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'name',
        'price_per_unit',
        'quantity',
        'total_cost',
        'purchase_date',
        'operation'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
