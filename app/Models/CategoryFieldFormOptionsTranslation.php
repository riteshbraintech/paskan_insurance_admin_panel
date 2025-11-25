<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFieldFormOptionsTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['option_id','lang_code','label','image'];

    // create image url accessor
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('/public/admin/option/img/' . $this->image);
        }
        return asset('/public/admin/banners/img/default.png');
    }
    
}
