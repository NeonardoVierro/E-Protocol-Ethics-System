<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class EthicalClearanceController extends Controller
{
    public function index()
    {
        return view('admin.ethicalclearance.index');
    }
}