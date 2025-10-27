<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use App\Scopes\AdminIdScope;
use App\Scopes\IsTestScope;
use Kyslik\ColumnSortable\Sortable;


class Bid extends Model
{
    use HasFactory,SoftDeletes,Sortable;

    protected $table = 'bids';

    protected $guarded = ['id'];
    public $sortable = ['bid_date','portal', 'project_type','created_at','bid_quote','updated_at','connects_needed'];

    protected static function booted()
    {
        
        static::addGlobalScope(new AdminIdScope);
        static::addGlobalScope(new IsTestScope);
    }

    public function lead(){
        return $this->hasOne(Lead::class, 'bid_id');
    }

    public function client(){
        return $this->hasOne(Client::class, 'id','client_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
