<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class CustomerLogin extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name', 'l_name', 'email', 'phone', 'password', 'loyalty_points', 'is_loyalty_enrolled',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class, Order::class, 'user_id', 'order_id')->latest();
    }
}
