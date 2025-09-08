<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()
            ->when($request->filled('role'), fn($x) => $x->where('role', $request->role))
            ->orderBy('created_at', 'desc');

        return view('admin.users.index', [
            'users' => $q->paginate(20),
            'filters' => [
                'role' => $request->role,
            ],
        ]);
    }

    public function toggleActive(User $user)
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('status', "User {$user->name} is now " . ($user->is_active ? 'active' : 'suspended') . '.');
    }

    public function toggleFlag(User $user)
    {
        $user->is_flagged = ! $user->is_flagged;
        $user->save();

        return back()->with('status', "User {$user->name} flag set to " . ($user->is_flagged ? 'FLAGGED' : 'ok') . '.');
    }
}
