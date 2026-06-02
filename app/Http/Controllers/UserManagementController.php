<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function searchUsers(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $search = "%{$q}%";

        $users = User::where('name', 'like', $search)
            ->orWhere('username', 'like', $search)
            ->orWhere('email', 'like', $search)
            ->limit(10)
            ->get();

        return response()->json(
            $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role === 'super_admin' ? 'Super Admin' : 'Admin',
                    'status' => $user->is_active ? 'Aktif' : 'Nonaktif',
                    'text' => $user->name . ' | Username: ' . $user->username . ' | Role: ' . ($user->role === 'super_admin' ? 'Super Admin' : 'Admin'),
                ];
            })
        );
    }

   public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('user_id')) {
            $query->whereKey($request->user_id);
        } elseif ($request->filled('search')) {
            $search = '%' . $request->search . '%';

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = new User([
            'role' => 'admin',
            'is_active' => true,
        ]);

        return view('users.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'role' => 'required|in:super_admin,admin',
            'is_active' => 'required|boolean',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|in:super_admin,admin',
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:8',
        ]);

        if ($user->id === Auth::id() && ($validated['role'] !== 'super_admin' || ! $validated['is_active'])) {
            return back()->with('error', 'Kamu tidak bisa menurunkan role atau menonaktifkan akun sendiri.');
        }

        if ($this->isLastActiveSuperAdmin($user) && ($validated['role'] !== 'super_admin' || ! $validated['is_active'])) {
            return back()->with('error', 'Minimal harus ada 1 Super Admin aktif.');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun sendiri.');
        }

        if ($this->isLastActiveSuperAdmin($user)) {
            return back()->with('error', 'Super Admin terakhir tidak boleh dihapus.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function isLastActiveSuperAdmin(User $user): bool
    {
        return $user->role === 'super_admin'
            && $user->is_active
            && User::where('role', 'super_admin')->where('is_active', true)->count() <= 1;
    }
}
