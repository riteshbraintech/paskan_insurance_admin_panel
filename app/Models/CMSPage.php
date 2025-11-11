<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;

class CMSPage extends Model
{
    use HasFactory, Sortable;

    // create fillable: title, slug, content, seo_title, seo_description, is_published

    protected $table = 'cms_pages';

    protected $fillable = [
        'page_title',
        'page_slug',
        'is_published',
    ];
    public $sortable = ['id', 'is_published'];

    // cast is_published to boolean
    protected $casts = [
        'is_published' => 'boolean',
    ];

    // set slug attribute to be lowercase and replace spaces with hyphens based on title before saving to database
    public function setSlugAttribute($value)
    {
        $this->attributes['page_slug'] = Str::slug($this->attributes['page_title']);
    }

    // create hasmany relationship with CMSPageTranslation
    public function translations()
    {
        return $this->hasMany(CMSPageTranslation::class, 'cms_page_id');
    }

    public function translation()
    {
        return $this->hasOne(CMSPageTranslation::class, 'cms_page_id')->where('lang_code', app()->getLocale());
    }


}
