<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // return only code and name as array
    public static function getLanguages() 
    {
        return self::select('code', 'name')->orderBy('is_default','desc')->get()->pluck('name','code')->toArray();
    }

}
