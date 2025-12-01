<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEnquery extends Model
{
    use HasFactory;
    protected $table = 'user_insurance_enqueries';
    protected $fillable=['user_id','category_id','enquery_time', 'status'];
}
