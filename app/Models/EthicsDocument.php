<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EthicsDocument extends Model
{
    use HasFactory;

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
        'signed_date' => 'date',
        'published_date' => 'date',
    ];

    // Konstanta status dokumen
    const STATUS_DRAFT = 'draft';
    const STATUS_SIGNED = 'signed';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    // ========== RELATIONSHIPS ==========
    
    // Proposal yang menghasilkan dokumen ini
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // Ketua yang menandatangani
    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    // Log dokumen
    public function logs()
    {
        return $this->hasMany(DocumentLog::class);
    }

    // ========== HELPER METHODS ==========
    
    // Tanda tangan dokumen
    public function sign($userId)
    {
        $this->ketua_id = $userId;
        $this->status = self::STATUS_SIGNED;
        $this->signed_date = now();
        $this->save();
    }

    // Publikasi dokumen
    public function publish()
    {
        $this->status = self::STATUS_PUBLISHED;
        $this->published_date = now();
        $this->save();
    }

    // Arsip dokumen
    public function archive()
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->save();
    }

    // Mendapatkan URL file
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    // Mendapatkan label status
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SIGNED => 'Sudah Ditandatangani',
            self::STATUS_PUBLISHED => 'Dipublikasikan',
            self::STATUS_ARCHIVED => 'Diarsipkan',
        ][$this->status] ?? $this->status;
    }
}