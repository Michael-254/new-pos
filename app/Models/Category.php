<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'position',
        'image',
        'status',
        'company_id',
        'created_at',
        'updated_at'
    ];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function childes()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
    public function scopePosition($query)
    {
        return $query->where('position', '=', 0);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
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
