<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CMSPageTranslation extends Model
{
    use SoftDeletes;

    protected $table = 'cms_page_translations';

    protected $fillable = [
        'cms_page_id', 'lang_code', 'title','slug', 'content',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    // create slug from title if not provided
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $model->slug = \Str::slug($model->title);
            }
        });
    }

    public function page()
    {
        return $this->belongsTo(CMSPage::class);
    }
}
