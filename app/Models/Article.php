<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Article extends Model
{
    use HasFactory,Sortable;
    protected $fillable = ['title','sort_order','image','is_active'];
    public $sortable = ['id', 'title', 'is_active'];

    // create image url accessor
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('/public/admin/articles/img/' . $this->image);
        }
    }

    // create hasmany relationship with CMSPageTranslation
    public function translations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id');
    }

    // crate a hasOne realtion to get translation in current app locale
    public function translation()
    {
        return $this->hasOne(ArticleTranslation::class, 'article_id')
        ->where('lang_code', app()->getLocale());
    }
}
