<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$proposalId = $argv[1] ?? null;
if (! $proposalId) {
    echo "Usage: php scripts/debug_assignments.php <proposal_id>\n";
    exit(1);
}

$rows = DB::table('proposal_assignments')->where('proposal_id', $proposalId)->get();
if ($rows->isEmpty()) {
    echo "No assignments for proposal_id={$proposalId}\n";
    exit(0);
}
foreach ($rows as $r) {
    echo "id={$r->id} role={$r->role} assigned_by={$r->assigned_by} assigned_to={$r->assigned_to} sent_at={$r->sent_at} due_date={$r->due_date} notes={$r->notes}\n";
}
