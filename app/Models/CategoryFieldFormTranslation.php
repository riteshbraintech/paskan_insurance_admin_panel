<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFieldFormTranslation extends Model
{
    use HasFactory;

    protected $table = 'category_field_form_translations';

    protected $fillable = [
        'categoryformfield_id',
        'lang_code',
        'label',
        'place_holder',
        'short_description',
        'options',
    ];

    public function field()
    {
        return $this->belongsTo(Categoryformfield::class);
    }
}
