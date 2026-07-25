<?php

// app/Http/Controllers/TwoFactorController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/admin/cashout');
        }

        return back()->with('error', 'Code invalide.');
    }

    public function setup()
    {
        $user = Auth::user();

        if (!$user->google2fa_enabled) {
            $user->enable2FA();
        }

        return view('auth.2fa-setup', [
            'qrCode' => $user->get2FAQRCode(),
            'secret' => $user->google2fa_secret,
        ]);
    }
}
