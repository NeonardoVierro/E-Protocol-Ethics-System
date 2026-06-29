<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProposalAssignment;
use App\Models\Review;

$proposalId = 10;
echo "ProposalAssignments:\n";
$assigns = ProposalAssignment::where('proposal_id', $proposalId)->get()->toArray();
print_r($assigns);

echo "\nReviews:\n";
$reviews = Review::where('proposal_id', $proposalId)->get()->toArray();
print_r($reviews);
