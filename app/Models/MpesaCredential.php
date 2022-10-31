<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaCredential extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'consumer_key',
        'consumer_secret',
        'test_consumer_key',
        'test_consumer_secret',
        'environment',
        'shortcode',
        'security_credential',
        'lipa_na_mpesa_passkey'
    ];
}
