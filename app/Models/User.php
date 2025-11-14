<?php

namespace App\Models;

use App\Models\Restaurant\FoodOrder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];
    public $sortable = ['id', 'name', 'is_active'];

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
        'marital_status',
        'nationality',
        'id_number',

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
