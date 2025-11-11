<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\IsTestScope;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope(new IsTestScope);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class, 'client_id');
    }

    public function getTotalBidsCount(){
        return $this->bids()->count();
    }
    public function leads()
    {
        return $this->hasMany(Lead::class, 'client_id');
    }
}
