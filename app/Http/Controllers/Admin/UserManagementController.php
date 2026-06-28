<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::pluck('name')->toArray();

        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'pending' => User::where('status', 'pending')->count(),
        ];

        return view('admin.usermanagement.index', compact('users', 'roles', 'stats'));
    }

    public function store(Request $request)
    {
        $roles = Role::pluck('name')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($roles)],
            'status' => ['required', Rule::in(['active', 'pending', 'inactive'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.usermanagement.index')
            ->with('success', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name')->toArray();
        return view('admin.usermanagement.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $roles = Role::pluck('name')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($roles)],
            'status' => ['required', Rule::in(['active', 'pending', 'inactive'])],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.usermanagement.index')->with('success', 'User berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        // set to a default password; adjust as needed (or generate random)
        $user->update(['password' => bcrypt('password')]);
        return redirect()->route('admin.usermanagement.index')->with('success', "Password untuk {$user->email} telah direset ke 'password'.");
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        return redirect()->route('admin.usermanagement.index')->with('success', 'Status user diperbarui.');
    }
}
