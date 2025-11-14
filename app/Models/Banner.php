<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['title','sort_order','image','is_active'];

    // create image url accessor
    public function getImageUrlAttribute()
    {
        // $imagePath = $item->image ? asset('/public/admin/categories/img/' . $item->image) : asset('/public/admin/categories/img/default.png');
        if ($this->image) {
            return asset('/public/admin/banners/img/' . $this->image);
        }
        return asset('/public/admin/banners/img/default.png');
    }



    // create hasmany relationship with CMSPageTranslation
    public function translations()
    {
        return $this->hasMany(BannerTranslation::class, 'banner_id');
    }

    // crate a hasOne realtion to get translation in current app locale
    public function translation()
    {
        return $this->hasOne(BannerTranslation::class, 'banner_id')
        ->where('lang_code', app()->getLocale());
    }
}
