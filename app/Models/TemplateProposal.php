<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TemplateProposal extends Model
{
    use HasFactory;

    protected $table = 'template_proposals';

    protected $fillable = [
        'nama_dokumen',
        'versi',
        'kategori',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessor: ukuran file human-readable ──────
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // ── Accessor: URL download ────────────────────
    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // ── Scope: hanya yang aktif ───────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}