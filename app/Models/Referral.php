<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    
    /**
     * Get all of the comments for the User
     *
     * @return BelongsTo
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(CustomerLogin::class);
    }
    
    /**
     * Get all of the comments for the User
     *
     * @return BelongsTo
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(CustomerLogin::class);
    }
}
