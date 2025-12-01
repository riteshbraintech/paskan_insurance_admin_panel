<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInsuranceFillup extends Model
{
    use HasFactory;

    protected $table = 'user_insurance_enqueries_details';

    protected $fillable=['user_insurance_enqueries_id', 'user_id','category_id','form_field_id', 'form_field_name','form_field_value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function formField()
    {
        return $this->belongsTo(Categoryformfield::class, 'formfieldname', 'id');
    }
}
