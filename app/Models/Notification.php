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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== HELPER METHODS ==========
    public function markAsRead()
    {
        $this->status = self::STATUS_READ;
        $this->read_at = now();
        $this->save();
    }

    public function markAsUnread()
    {
        $this->status = self::STATUS_UNREAD;
        $this->read_at = null;
        $this->save();
    }

    public function isRead()
    {
        return $this->status === self::STATUS_READ;
    }

    // Mendapatkan label tipe untuk frontend
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
    
    // Mendapatkan icon untuk frontend
    public function getIconAttribute()
    {
        return [
            self::TYPE_ACCOUNT_ACTIVATION => 'check_circle',
            self::TYPE_REVIEW_ASSIGNMENT => 'assignment',
            self::TYPE_PROPOSAL_STATUS => 'article',
            self::TYPE_DOCUMENT_READY => 'description',
            self::TYPE_REVISION_REQUEST => 'edit_note',
        ][$this->type] ?? 'notifications';
    }
    
    // Mendapatkan warna untuk frontend
    public function getIconColorAttribute()
    {
        return [
            self::TYPE_ACCOUNT_ACTIVATION => 'text-emerald-600 bg-emerald-50',
            self::TYPE_REVIEW_ASSIGNMENT => 'text-blue-600 bg-blue-50',
            self::TYPE_PROPOSAL_STATUS => 'text-primary bg-primary/10',
            self::TYPE_DOCUMENT_READY => 'text-secondary bg-secondary/10',
            self::TYPE_REVISION_REQUEST => 'text-amber-600 bg-amber-50',
        ][$this->type] ?? 'text-on-surface-variant bg-surface-container-low';
    }
}