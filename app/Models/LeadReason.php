<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadReason extends Model
{
    use HasFactory;
    protected $table = 'lead_reasons';
    protected $fillable = ['lead_id', 'admin_id', 'reason'];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
