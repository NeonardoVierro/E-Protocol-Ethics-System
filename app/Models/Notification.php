<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'status',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Konstanta tipe notifikasi
    const TYPE_ACCOUNT_ACTIVATION = 'account_activation';
    const TYPE_REVIEW_ASSIGNMENT = 'review_assignment';
    const TYPE_PROPOSAL_STATUS = 'proposal_status';
    const TYPE_DOCUMENT_READY = 'document_ready';
    const TYPE_REVISION_REQUEST = 'revision_request';

    // Konstanta status
    const STATUS_READ = 'read';
    const STATUS_UNREAD = 'unread';

    // ========== RELATIONSHIPS ==========
    
    // User penerima notifikasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== HELPER METHODS ==========
    
    // Tandai sebagai sudah dibaca
    public function markAsRead()
    {
        $this->status = self::STATUS_READ;
        $this->read_at = now();
        $this->save();
    }

    // Tandai sebagai belum dibaca
    public function markAsUnread()
    {
        $this->status = self::STATUS_UNREAD;
        $this->read_at = null;
        $this->save();
    }

    // Cek apakah sudah dibaca
    public function isRead()
    {
        return $this->status === self::STATUS_READ;
    }

    // Mendapatkan label tipe
    public function getTypeLabelAttribute()
    {
        return [
            self::TYPE_ACCOUNT_ACTIVATION => 'Aktivasi Akun',
            self::TYPE_REVIEW_ASSIGNMENT => 'Penugasan Review',
            self::TYPE_PROPOSAL_STATUS => 'Status Proposal',
            self::TYPE_DOCUMENT_READY => 'Dokumen Siap',
            self::TYPE_REVISION_REQUEST => 'Permintaan Revisi',
        ][$this->type] ?? $this->type;
    }
}