<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFieldFormOptions extends Model
{
    use HasFactory;
    protected $table='category_form_fields_options';
    protected $fillable = ['field_id','value','order'];

    // create hasmany relationship with FormfieldOptionTranslation
    public function translations()
    {
        return $this->hasMany(CategoryFieldFormOptionsTranslation::class, 'option_id');
    }

    // crate a hasOne realtion to get translation in current app locale
    public function translation()
    {
        return $this->hasOne(CategoryFieldFormOptionsTranslation::class, 'option_id')
        ->where('lang_code', app()->getLocale());
    }

    /**
     * The roles that belong to the CategoryFieldFormOptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function optionIds()
    {
        return $this->belongsToMany(Self::class, 'category_form_fields_options_relation', 'option_id', 'parent_option_id');
    }

}
