<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Scopes\AdminIdScope;
use App\Scopes\IsTestScope;
use App\Scopes\IsClonedScope;
use Kyslik\ColumnSortable\Sortable;

class Lead extends Model
{
    use HasFactory,SoftDeletes, Sortable;

    protected $table = 'leads';

    protected $guarded = ['id'];

    

    public $sortable = ['lead_id','bid_date','next_followup','created_at','portal','project_type','status','client_budget','updated_at'];

    protected static function booted()
    {
        static::addGlobalScope(new AdminIdScope);
        static::addGlobalScope(new IsTestScope);
        static::addGlobalScope(new IsClonedScope);
    }

    public function remarks(){
        return $this->hasMany(LeadRemark::class, 'lead_id')->latest();
    }
    
    public function attachments(){
        return $this->hasMany(LeadAttachment::class,'lead_id')->latest();
    }

    public function budgets(){
        return $this->hasMany(BudgetHistory::class,'lead_id');
    }

    public function logs(){
        return $this->hasMany(Log::class, 'lead_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }
}
