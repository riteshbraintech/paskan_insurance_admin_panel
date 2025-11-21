<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;
    protected $fillable = ['admin_id','event_name','event_type','notes'];

    protected $casts = [
        'notes' => 'array',
    ];
}
