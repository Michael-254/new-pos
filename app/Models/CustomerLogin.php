<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLogin extends Model
{
    use HasFactory;

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class, Order::class, 'user_id', 'order_id')->latest();
    }
}
