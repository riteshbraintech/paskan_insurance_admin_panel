<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaimTranslation extends Model
{
    use HasFactory;
    // protected $table = 'insurance_claim_translations';

    protected $fillable = [
        'insurance_claim_id',
        'lang_code',
        'title',
        'description',
    ];

     public function page()
    {
        return $this->belongsTo(InsuranceClaim::class);
    }
}
