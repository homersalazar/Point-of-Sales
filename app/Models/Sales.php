<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_no', 'customer_id', 'user_id', 'total_amount', 'payment_method', 'payment_status'];
}

