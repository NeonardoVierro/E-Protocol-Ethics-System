<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RoleAndPermissionController extends Controller
{
    public function index()
    {
        return view('admin.role&permission.index');
    }
}