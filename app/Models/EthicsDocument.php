<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EthicsDocument extends Model
{
    use HasFactory;

    protected $table = 'ethics_documents';

    protected $fillable = [
        'proposal_id',
        'document_number',
        'ketua_id',
        'status',
        'file_path',
        'original_name',
        'signed_date',
        'published_date',
        'notes',
    ];

    protected $casts = [
        'signed_date'    => 'date',
        'published_date' => 'date',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_SIGNED    = 'signed';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED  = 'archived';

    // ── Relationships ─────────────────────────────

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    // ── Helpers ───────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return [
            self::STATUS_DRAFT     => 'Draft',
            self::STATUS_SIGNED    => 'Ditandatangani',
            self::STATUS_PUBLISHED => 'Dipublikasi',
            self::STATUS_ARCHIVED  => 'Diarsipkan',
        ][$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return [
            self::STATUS_DRAFT     => 'bg-slate-100 text-slate-600',
            self::STATUS_SIGNED    => 'bg-blue-100 text-blue-700',
            self::STATUS_PUBLISHED => 'bg-emerald-100 text-emerald-700',
            self::STATUS_ARCHIVED  => 'bg-orange-100 text-orange-700',
        ][$this->status] ?? 'bg-slate-100 text-slate-600';
    }
}