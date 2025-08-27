<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    // (optional) deactivate method later
}
