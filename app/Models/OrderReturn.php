<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'payment_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public static function boot()
    {
        parent::boot();

        if (auth('admin')->user()) {
            static::addGlobalScope('company_id', function (Builder $builder) { // before return customer method call this
                $builder->where('company_id', auth('admin')->user()->company_id);
            });
        } elseif (auth()->user()) {
            static::addGlobalScope('company_id', function (Builder $builder) { // before return customer method call this
                $builder->where('company_id', auth()->user()->company_id);
            });
        } else {
            static::addGlobalScope('company_id', function (Builder $builder) { // before return customer method call this
                $builder->where('company_id', 1);
            });
        }
    }
}
