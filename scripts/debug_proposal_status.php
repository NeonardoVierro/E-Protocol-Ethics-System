<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$proposalId = $argv[1] ?? null;
if (! $proposalId) {
    echo "Usage: php scripts/debug_proposal_status.php <proposal_id>\n";
    exit(1);
}

$p = DB::table('proposals')->where('id', $proposalId)->first();
if (! $p) {
    echo "Proposal not found: {$proposalId}\n";
    exit(1);
}
echo "proposal_id={$p->id} status={$p->status} review_type={$p->review_type} sek_id={$p->sekretaris_id} submission_date={$p->submission_date}\n";

$revs = DB::table('proposal_revisions')->where('proposal_id', $proposalId)->get();
if ($revs->isEmpty()) {
    echo "No revisions found.\n";
} else {
    foreach ($revs as $r) {
        echo "rev id={$r->id} status={$r->status} revision_number={$r->revision_number} file_id={$r->file_id} submitted_date={$r->submitted_date} requested_date={$r->requested_date}\n";
    }
}

$assigns = DB::table('proposal_assignments')->where('proposal_id', $proposalId)->get();
if ($assigns->isEmpty()) {
    echo "No assignments found.\n";
} else {
    foreach ($assigns as $a) {
        echo "assign id={$a->id} role={$a->role} assigned_by={$a->assigned_by} assigned_to={$a->assigned_to} sent_at={$a->sent_at} due_date={$a->due_date} notes={$a->notes}\n";
    }
}
