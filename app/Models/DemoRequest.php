<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'site_visitor_count',
        'status',
        'notes'
    ];

    protected $casts = [
        'site_visitor_count' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
