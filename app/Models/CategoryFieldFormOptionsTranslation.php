<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFieldFormOptionsTranslation extends Model
{
    use HasFactory;
    protected $table = 'category_form_fields_option_transalations';
    protected $fillable = ['option_id','lang_code','label','image'];

    public $casts = [
        'image' => 'string',
    ];

    protected $appends = ['image_url'];


    // create image url accessor
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('/public/admin/form_options/' . $this->image);
        }
        return asset('/public/admin/banners/img/default.png');
    }
    
}
