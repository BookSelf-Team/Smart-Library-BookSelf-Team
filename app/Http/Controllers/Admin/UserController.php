<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['nullable', 'string', 'max:50', 'unique:users,identifier'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(Role::assignableValues())],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'identifier' => $validated['identifier']
                ?? User::generateUniqueIdentifier(Role::from($validated['role'])),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan dengan identifier '.$user->identifier.'.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['nullable', 'string', 'max:50', Rule::unique('users', 'identifier')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(Role::assignableValues())],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $validated['name'],
            // Identifier kosong memakai identifier lama; pengguna tanpa identifier
            // mendapat nomor baru secara otomatis.
            'identifier' => $validated['identifier']
                ?? $user->identifier
                ?? User::generateUniqueIdentifier(Role::from($validated['role'])),
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];
        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus pengguna.');
        }
    }
}
