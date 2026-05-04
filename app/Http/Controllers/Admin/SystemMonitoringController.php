<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SystemMonitoringController extends Controller
{
    public function index()
    {
        return view('admin.systemmonitoring.index');
    }
}