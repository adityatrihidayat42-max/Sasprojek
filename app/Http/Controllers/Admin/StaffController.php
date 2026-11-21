<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.staff', compact('users'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|max:120|unique:users,email',
            'password' => 'required|string|min:8|max:100',
            'role' => 'required|in:admin,cashier,kitchen',
        ]);
        User::create($d);
        return back()->with('success', 'Akun staf ditambahkan.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['staff' => 'Anda tidak bisa menghapus akun sendiri.']);
        }
        $user->delete();
        return back()->with('success', 'Akun staf dihapus.');
    }
}
