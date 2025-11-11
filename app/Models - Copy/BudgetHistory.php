<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHistory extends Model
{
    use HasFactory;

    protected $table = 'budget_histories';

    protected $guarded = ['id'];

    public function lead(){
        return $this->belongsTo(Lead::class,'lead_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id');
    }
}
