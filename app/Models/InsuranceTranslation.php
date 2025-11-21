<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'insurance_id',
        'lang_code',
        'title',
        'description',
    ];

    public function page()
    {
        return $this->belongsTo(Insurance::class);
    }
}
