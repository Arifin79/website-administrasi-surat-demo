<?php

namespace App\Http\Controllers;

use App\Data\UserData;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->get()->map(fn($m) => UserData::fromModel($m));

        return view('pengguna.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:60', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', 'in:admin,staf'],
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        ActivityLog::record(session('auth_user.id'), 'create_user', "Menambahkan pengguna: {$request->username}", $request->ip());

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, string $username): RedirectResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'new_username'=> ['required', 'string', 'max:60',
                              'unique:users,username,' . $user->id],
            'role'        => ['required', 'in:admin,staf'],
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->new_username,
            'role'     => $request->role,
        ]);

        ActivityLog::record(session('auth_user.id'), 'update_user', "Memperbarui pengguna: {$request->new_username}", $request->ip());

        return redirect()->route('pengguna.index')
            ->with('success', "Data akun {$request->new_username} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, string $username): RedirectResponse
    {
        $request->validate([
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::where('username', $username)->firstOrFail();

        $user->update(['password' => Hash::make($request->password_baru)]);

        ActivityLog::record(session('auth_user.id'), 'reset_password', "Reset password pengguna: {$username}", $request->ip());

        return redirect()->route('pengguna.index')
            ->with('success', "Password akun {$username} berhasil direset.");
    }

    public function destroy(string $username): RedirectResponse
    {
        if ($username === 'admin') {
            return redirect()->route('pengguna.index')
                ->with('error', 'Akun administrator utama tidak dapat dihapus.');
        }

        $user = User::where('username', $username)->firstOrFail();
        $user->delete();

        ActivityLog::record(session('auth_user.id'), 'delete_user', "Menghapus pengguna: {$username}");

        return redirect()->route('pengguna.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
