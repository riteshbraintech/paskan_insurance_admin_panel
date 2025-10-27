<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $guarded = [
        'id'
    ];
    
    const SUPERADMIN = 'superadmin';
    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const STAFF = 'staff';

    const ROLEARR = ['admin', 'manager', 'staff'];

    protected $appends = ['total_user_count']; // append extra column to role 

    /*
        Get Total User Count with append in role
    */
    public function getTotalUserCountAttribute(){
        return Admin::query()->testfilter()->where('role_id', $this->role_slug)->count();
    }

    /*
        Module Permission List Decoding
    */
    public function getModulePermissionsAttribute($value)
    {
        return json_decode($value);
    }



}
