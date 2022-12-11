<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    // public static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('company_id', function (Builder $builder) { // before return user method call this
    //         $builder->where('company_id', auth('admin')->user()->company_id);
    //     });
    // }
}
