<?php

namespace App\Models;

use App\Traits\HasActionButtons;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Attachment extends Model implements Auditable
{
    use AuditingAuditable, HasActionButtons, HasFactory, SoftDeletes;

    protected $fillable = [
        'attachment_type_id',
        'file_name',
        'file_path',
        'file_hash',
        'file_name',
        'attachable_type',
        'attachable_id',
        'source',
        'file_type',
        'file_size',
        'file_extension',
    ];

    public const ui = [
        'table' => 'attachments',
        'route' => 'attachments',
        's_ucf' => 'Attachment',
        'p_ucf' => 'Attachments',
        's_lcf' => 'attachment',
        'p_lcf' => 'attachments',
        'view' => 'CP.attachments.',
        '_id' => 'attachment_id',
        'controller_name' => 'AttachmentController',
        'image_path' => 'attachments',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function attachment_type()
    {
        return $this->belongsTo(Constant::class, 'attachment_type_id');
    }
}
