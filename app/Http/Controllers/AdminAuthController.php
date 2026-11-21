<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (EnsureAdmin::isAuthorized($request)) {
            return redirect()->route('family.index');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:200',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $expectedUser = (string) config('admin.username', 'admin');
        $expectedPass = (string) config('admin.password', 'munadi2026');

        $userMatch = hash_equals($expectedUser, trim($credentials['username']));
        $passMatch = hash_equals($expectedPass, $credentials['password']);

        if (!$userMatch || !$passMatch) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username atau kata sandi admin tidak sesuai.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_username', $expectedUser);

        $intended = $request->session()->pull('url.intended', route('family.index'));

        return redirect($intended)->with('success', 'Berhasil masuk ke Mode Admin. Anda kini dapat menambah & mengedit data keluarga.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['is_admin', 'admin_username']);
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari Mode Admin.');
    }
}
