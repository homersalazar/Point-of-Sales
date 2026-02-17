<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase_item extends Model
{
    use HasFactory;
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'cost_price', 'subtotal'];
}
