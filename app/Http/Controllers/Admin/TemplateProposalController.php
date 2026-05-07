<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TemplateProposalController extends Controller
{
    public function index()
    {
        return view('admin.templateproposal.index');
    }
}