<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Role;
use App\Models\AdminType;
use App\Models\Restaurant\Restaurant;
use App\Scopes\IsTestScope;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $table = 'admins';

    protected $with = ['role'];

    const SUPERADMIN = "superadmin";

    public function scopeTestFilter(Builder $query)
    {
        if (admin()->check() && admin()->user()->role_id !== 'superadmin') {
            $isTest = admin()->user()->is_test;
            $query->where('is_test', $isTest);
        }
        
        if (admin()->check() && admin()->user()->role_id == 'superadmin') {
            $istest = session('is_test') ? session('is_test') : false;
            $val = $istest ? 1:0;
            $query->where('is_test', $val);
        }

    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        
    ];
    /*
        Format DOB format
    */
    // public function getDateOfBirthAttribute($value)
    // {
    //     return date("d/m/Y", strtotime($value));
    // }



    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function createdby(){
        return $this->belongsTo(Admin::class,'created_by','id');
    }

}
