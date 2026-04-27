<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'revision_number',
        'revision_note',
        'requested_date',
        'submitted_date',
        'status',
        'file_id',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'submitted_date' => 'date',
    ];

    // Konstanta status revisi
    const STATUS_REQUESTED = 'requested';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    // ========== RELATIONSHIPS ==========
    
    // Proposal yang direvisi
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // File revisi
    public function file()
    {
        return $this->belongsTo(ProposalFile::class, 'file_id');
    }

    // ========== HELPER METHODS ==========
    
    // Submit revisi
    public function submit($fileId)
    {
        $this->file_id = $fileId;
        $this->status = self::STATUS_SUBMITTED;
        $this->submitted_date = now();
        $this->save();
        
        // Update status proposal menjadi revised
        $this->proposal->updateStatus(Proposal::STATUS_REVISED);
    }

    // Terima revisi
    public function accept()
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->save();
    }

    // Tolak revisi
    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }

    // Mendapatkan label status
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_REQUESTED => 'Revisi Diminta',
            self::STATUS_IN_PROGRESS => 'Sedang Direvisi',
            self::STATUS_SUBMITTED => 'Revisi Dikirim',
            self::STATUS_ACCEPTED => 'Revisi Diterima',
            self::STATUS_REJECTED => 'Revisi Ditolak',
        ][$this->status] ?? $this->status;
    }
}