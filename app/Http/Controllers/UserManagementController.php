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
        $this->ensureCanManageUsers();

        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $search = "%{$q}%";

        $users = User::whereIn('role', $this->manageableRoles())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('email', 'like', $search);
            })
            ->limit(10)
            ->get();

        $roleLabels = User::roleLabels();

        return response()->json(
            $users->map(function ($user) use ($roleLabels) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $roleLabels[$user->role] ?? $user->role,
                    'status' => $user->is_active ? 'Aktif' : 'Nonaktif',
                    'text' => $user->name . ' | Username: ' . $user->username . ' | Role: ' . ($roleLabels[$user->role] ?? $user->role),
                ];
            })
        );
    }

   public function index(Request $request)
    {
        $this->ensureCanManageUsers();

        $query = User::whereIn('role', $this->manageableRoles());

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

        if ($request->filled('role') && in_array($request->role, $this->manageableRoles(), true)) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(50)->withQueryString();
        $roleOptions = $this->roleOptions();

        return view('users.index', compact('users', 'roleOptions'));
    }

    public function create()
    {
        $this->ensureCanManageUsers();

        $user = new User([
            'role' => $this->defaultRole(),
            'is_active' => true,
        ]);
        $roleOptions = $this->roleOptions();

        return view('users.create', compact('user', 'roleOptions'));
    }

    public function store(Request $request)
    {
        $this->ensureCanManageUsers();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'role' => ['required', Rule::in($this->manageableRoles())],
            'is_active' => 'required|boolean',
            'password' => 'required|string|min:3',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->ensureCanManageUsers();
        $this->ensureCanManageTarget($user);

        $roleOptions = $this->roleOptions();

        return view('users.edit', compact('user', 'roleOptions'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureCanManageUsers();
        $this->ensureCanManageTarget($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($this->manageableRoles())],
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:3',
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
        $this->ensureCanManageUsers();
        $this->ensureCanManageTarget($user);

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

    private function ensureCanManageUsers(): void
    {
        abort_unless(Auth::user()?->canManageUsers(), 403);
    }

    private function ensureCanManageTarget(User $user): void
    {
        abort_unless(Auth::user()?->isSuperAdmin() || in_array($user->role, ['client', 'staf'], true), 403);
    }

    private function manageableRoles(): array
    {
        if (Auth::user()?->isSuperAdmin()) {
            return ['super_admin', 'admin', 'client', 'staf'];
        }

        return ['client', 'staf'];
    }

    private function roleOptions(): array
    {
        return array_intersect_key(User::roleLabels(), array_flip($this->manageableRoles()));
    }

    private function defaultRole(): string
    {
        return Auth::user()?->isSuperAdmin() ? 'admin' : 'client';
    }
}
