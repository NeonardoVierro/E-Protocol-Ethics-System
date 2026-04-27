<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'file_path',
        'file_type',
        'original_name',
        'file_size',
        'mime_type',
        'version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    // Konstanta tipe file
    const TYPE_PROPOSAL = 'proposal_document';
    const TYPE_SUPPORTING = 'supporting_document';
    const TYPE_REVISION = 'revision_document';

    // ========== RELATIONSHIPS ==========
    
    // Proposal pemilik file
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // Revisi yang menggunakan file ini
    public function revision()
    {
        return $this->hasOne(ProposalRevision::class);
    }

    // ========== HELPER METHODS ==========
    
    // Mendapatkan URL lengkap file
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    // Mendapatkan label tipe file
    public function getTypeLabelAttribute()
    {
        return [
            self::TYPE_PROPOSAL => 'Dokumen Proposal',
            self::TYPE_SUPPORTING => 'Dokumen Pendukung',
            self::TYPE_REVISION => 'Dokumen Revisi',
        ][$this->file_type] ?? $this->file_type;
    }
}