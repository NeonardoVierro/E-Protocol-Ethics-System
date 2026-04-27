<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ethics_document_id',
        'proposal_id',
        'user_id',
        'activity',
        'ip_address',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Konstanta aktivitas
    const ACTIVITY_UPLOAD = 'upload';
    const ACTIVITY_DOWNLOAD = 'download';
    const ACTIVITY_VIEW = 'view';
    const ACTIVITY_SIGN = 'sign';
    const ACTIVITY_PUBLISH = 'publish';
    const ACTIVITY_ARCHIVE = 'archive';
    const ACTIVITY_DELETE = 'delete';
    const ACTIVITY_UPDATE = 'update';
    const ACTIVITY_ASSIGN = 'assign';
    const ACTIVITY_VERIFY = 'verify';

    // ========== RELATIONSHIPS ==========
    
    // Dokumen ethics yang terkait
    public function ethicsDocument()
    {
        return $this->belongsTo(EthicsDocument::class, 'ethics_document_id');
    }

    // Proposal yang terkait
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // User yang melakukan aktivitas
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== HELPER METHODS ==========
    
    // Mendapatkan label aktivitas
    public function getActivityLabelAttribute()
    {
        return [
            self::ACTIVITY_UPLOAD => 'Upload',
            self::ACTIVITY_DOWNLOAD => 'Download',
            self::ACTIVITY_VIEW => 'Lihat',
            self::ACTIVITY_SIGN => 'Tanda Tangan',
            self::ACTIVITY_PUBLISH => 'Publikasi',
            self::ACTIVITY_ARCHIVE => 'Arsip',
            self::ACTIVITY_DELETE => 'Hapus',
            self::ACTIVITY_UPDATE => 'Update',
            self::ACTIVITY_ASSIGN => 'Assign',
            self::ACTIVITY_VERIFY => 'Verifikasi',
        ][$this->activity] ?? $this->activity;
    }
}