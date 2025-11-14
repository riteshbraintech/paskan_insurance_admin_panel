<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;

class FAQ extends Model
{
    use HasFactory,Sortable;
    protected $fillable = ['title','slug','is_published','sort_order'];
    public $sortable = ['id','sort_order', 'title', 'is_published'];

    // set slug attribute to be lowercase and replace spaces with hyphens based on title before saving to database
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($this->attributes['title']);
    }

    // create hasmany relationship with CMSPageTranslation
    public function translations()
    {
        return $this->hasMany(FAQTranslation::class, 'faq_id');
    }

    // crate a hasOne realtion to get translation in current app locale
    public function translation()
    {
        return $this->hasOne(FAQTranslation::class, 'faq_id')
        ->where('lang_code', app()->getLocale());
    }

}
