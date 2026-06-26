<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'product_name',
        'product_image',
        'quantity',
        'total_price',
        'address',
        'location',
        'state',
        'pincode',
        'status',
        'payment_status',
    ];
}