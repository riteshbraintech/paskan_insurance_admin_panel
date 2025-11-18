<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Categoryformfield extends Model
{
    use HasFactory,Sortable;

    protected $table = 'categoryformfields';

    protected $guarded = ['id'];
    public $sortable = ['id', 'label','sort_order','is_required'];

    protected $casts = [
        'images' => 'array',
        'options' => 'array',
    ];

     // create image url accessor
    public function getImageUrlsAttribute()
    {
        $images = $this->images;

        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        if (!is_array($images)) {
            return [];
        }

        return array_map(function ($img) {

            if (str_starts_with($img, 'http')) {
                return $img;
            }

            return asset('public/'.$img);

        }, $images);
    }


    public function translations()
    {
        return $this->hasMany(CategoryFieldFormTranslation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function translation()
    {
        return $this->hasOne(CategoryFieldFormTranslation::class, 'categoryformfield_id')
        ->where('lang_code', app()->getLocale());
    }
    
}


