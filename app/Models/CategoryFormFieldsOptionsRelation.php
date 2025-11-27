<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFormFieldsOptionsRelation extends Model
{
    use HasFactory;
    protected $table = 'category_form_fields_options_relation';
    protected $fillable = ['option_id','parent_option_id'];
}
