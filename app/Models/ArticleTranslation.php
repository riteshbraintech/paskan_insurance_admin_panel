<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_id','lang_code','title','subtitle','content','meta_title', 'meta_description', 'meta_keywords'
    ];

    public function page()
    {
        return $this->belongsTo(Article::class);
    }
}
