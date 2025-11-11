<?php

namespace App\Models;

use App\Scopes\AdminIdScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\IsTestScope;

class Log extends Model
{
    use HasFactory;
    protected $table = 'logs';
    protected $guarded = ['id'];
    protected static function booted()
    {
        static::addGlobalScope(new IsTestScope);
    }
}
