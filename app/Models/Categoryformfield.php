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
    public $sortable = ['id', 'parent_field_id','name','sort_order','is_required'];


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

    // relation to own field
    public function parent(){
        return $this->hasOne(Categoryformfield::class, 'id','parent_field_id');
    }

    public function options()
    {
        return $this->hasMany(CategoryFieldFormOptions::class,'field_id','id');
    }
    


    
}


