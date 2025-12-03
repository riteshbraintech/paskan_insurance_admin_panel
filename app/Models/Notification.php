<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','type','title','message','link','is_read'];

    // Optional: mark as read
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }

    // Relation to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
