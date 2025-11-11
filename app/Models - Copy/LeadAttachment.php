<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadAttachment extends Model
{
    use HasFactory;

    protected $table = 'lead_attachments';

    protected $guarded = ['id'];

    const ATTACHMENT_PATH = 'admin/upload/lead/';

    protected $appends = ['attachment_url'];

    public function getAttachmentUrlAttribute(){
        $fullUrl = loadAssets('upload/lead/'.$this->attachment);
        return $fullUrl;
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
