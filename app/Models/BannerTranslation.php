<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_id',
        'lang_code',
        'title',
        'sub_title',
        'description',
    ];

    public function page()
    {
        return $this->belongsTo(Banner::class);
    }
}
