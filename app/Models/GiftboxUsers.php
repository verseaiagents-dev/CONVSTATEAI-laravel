<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftboxUsers extends Model
{
    protected $table = 'giftbox_users';
    
    protected $fillable = [
        'name',
        'surname', 
        'mail',
        'phone',
        'visitors',
        'sector'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
