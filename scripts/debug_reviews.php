<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Review;

$proposalId = $argv[1] ?? null;
if (! $proposalId) {
    echo "Usage: php scripts/debug_reviews.php <proposal_id>\n";
    exit(1);
}

$rows = DB::table('reviews')->where('proposal_id', $proposalId)->get();

if ($rows->isEmpty()) {
    echo "No reviews found for proposal_id={$proposalId}\n";
    exit(0);
}

foreach ($rows as $r) {
    echo "id={$r->id} reviewer_id={$r->reviewer_id} status={$r->status} assigned_date={$r->assigned_date} due_date={$r->due_date} completed_date={$r->completed_date}\n";
}
