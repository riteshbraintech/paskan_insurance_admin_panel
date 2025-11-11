<?php

namespace App\Models;

use App\Models\Restaurant\FoodOrder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];

    protected $fillable = [
        'member_id',
        'certification_id',
        'name',
        'email',
        'password',
        'dial_code',
        'phone',
        'gender',
        'dob',
        'country_id',
        'address',
        'postcode',
        'wallet_amount',
        'remarks',
        'subscribed',
        'status',
        'is_active',
        'device_name',
        'device_type',
        'device_id',
        'firebase_token',
        'image',
    ];

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
        'phone_verified_at' => 'datetime',
    ];

    // public function address()
    // {
    //     return $this->hasOne(UserAddress::class, 'user_id')->latestOfMany();
    // }

    // public function orders()
    // {
    //     return $this->hasMany(FoodOrder::class, 'user_id');
    // }
}
