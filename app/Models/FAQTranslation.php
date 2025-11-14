<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['lang_code','faq_id','title','description'];

    public function page()
    {
        return $this->belongsTo(FAQ::class);
    }
}
