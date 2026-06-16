<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProposalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'user_id',
        'note_type',
        'content',
        'file_path',
        'original_name',
    ];

    const TYPE_SECRETARY = 'secretary';
    const TYPE_REVIEWER = 'reviewer';
    const TYPE_GENERAL = 'general';

    // ========== RELATIONSHIPS ==========
    
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== HELPER METHODS ==========
    
    public function getNoteTypeLabel()
    {
        return [
            self::TYPE_SECRETARY => 'Catatan Sekretaris',
            self::TYPE_REVIEWER => 'Catatan Reviewer',
            self::TYPE_GENERAL => 'Catatan Umum',
        ][$this->note_type] ?? $this->note_type;
    }
}
