<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

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
