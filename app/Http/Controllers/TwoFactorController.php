<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected $twoFactor;

    public function __construct(TwoFactorService $twoFactor)
    {
        $this->twoFactor = $twoFactor;
    }

    public function prompt()
    {
        // Cegah akses prompt jika sudah verifikasi
        if (session('2fa_verified')) {
            return match (Auth::user()->roles) {
                'Owner' => to_route('admin.dashboard'),
                'Customer' => to_route('customer.dashboard'),
                default => to_route('home'),
            };
        }
        return view('auth.prompt-twofa');
    }

    public function verifyLogin(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = Auth::user();

        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return back()->withErrors(['code' => '2FA is not setup for this account.']);
        }

        // Validasi kode OTP atau recovery
        $isValid = $this->twoFactor->verifyLoginCode($user, $request->code);

        if (!$isValid) {
            if ($request->input('source') === 'recovery') {
                // Jika berasal dari recovery modal, kirim JSON error (untuk ditangani JS)
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid recovery code. Please try again.',
                ], 422);
            }

            // Jika dari OTP biasa
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        // Tandai session sudah diverifikasi
        session(['2fa_verified' => true]);

        // Redirect sesuai role
        $redirect = match ($user->roles) {
            'Owner' => route('admin.dashboard'),
            'Customer' => route('customer.dashboard'),
            default => route('home'),
        };

        if ($request->input('source') === 'recovery') {
            // Jika sukses dari recovery code, kirim redirect JSON ke JS
            return response()->json([
                'status' => true,
                'redirect' => $redirect,
            ]);
        }

        // Jika sukses dari OTP biasa
        return redirect()->to($redirect);
    }


    public function setup()
    {
        $user = Auth::user();
        $result = $this->twoFactor->setup2FA($user);

        return response()->json($result);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = Auth::user();

        $result = $this->twoFactor->verify2FA($user, $request->code);

        if (!$result['status']) {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }

        return response()->json([
            'message' => '2FA enabled',
            'recovery_codes' => $result['recovery_codes'],
        ]);
    }

    public function markRecoveryDownloaded()
    {
        $this->twoFactor->markCodesDownloaded(Auth::user());
        return response()->json(['status' => true]);
    }

    public function disable()
    {
        $this->twoFactor->disable2FA(Auth::user());
        return response()->json(['message' => '2FA disabled']);
    }
}
