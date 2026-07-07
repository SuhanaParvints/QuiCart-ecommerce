<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'customer_name',
        'customer_email',
        'product_name',
        'rating',
        'experience',
        'comment',
        'image',
        'status',
    ];
}