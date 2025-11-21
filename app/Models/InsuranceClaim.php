<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class InsuranceClaim extends Model
{
    use HasFactory,Sortable;
    protected $fillable = ['insurance_id','title','is_published','sort_order'];
    public $sortable = ['id', 'title','sort_order'];

    public function translations()
    {
        return $this->hasMany(InsuranceClaimTranslation::class);
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class, 'insurance_id');
    }

    public function translation()
    {
        return $this->hasOne(InsuranceClaimTranslation::class, 'insurance_claim_id')
        ->where('lang_code', app()->getLocale());
    }
}
