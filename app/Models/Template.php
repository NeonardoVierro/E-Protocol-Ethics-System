<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'document_number',
        'file_path',
        'original_name',
        'version',
        'type',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Konstanta tipe template
    const TYPE_PROPOSAL = 'proposal_template';
    const TYPE_ETHICS = 'ethics_template';
    const TYPE_OTHER = 'other';

    // ========== RELATIONSHIPS ==========
    
    // User yang membuat template
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========== HELPER METHODS ==========
    
    // Aktifkan template
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    // Non-aktifkan template
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    // Mendapatkan URL file
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    // Mendapatkan label tipe
    public function getTypeLabelAttribute()
    {
        return [
            self::TYPE_PROPOSAL => 'Template Proposal',
            self::TYPE_ETHICS => 'Template Ethical Clearance',
            self::TYPE_OTHER => 'Lainnya',
        ][$this->type] ?? $this->type;
    }
}