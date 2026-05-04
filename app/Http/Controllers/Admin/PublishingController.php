<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PublishingController extends Controller
{
    public function index()
    {
        return view('admin.publishing.index');
    }
}