<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TwoFactorRepository
{
    public function storeSecretTemp($user, $secret)
    {
        session(['2fa_secret' => $secret]);
    }

    public function generateRecoveryCodes()
    {
        $codes = collect(range(1, 10))->map(function () {
            return strtoupper(Str::random(10));
        })->toArray();

        return $codes;
    }

    public function enable2FA($user, $secret, array $recoveryCodes)
    {
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_enabled = true;
        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->two_factor_codes_downloaded = false;
        $user->save();
    }

    public function disable2FA($user)
    {
        $user->two_factor_secret = null;
        $user->two_factor_enabled = false;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_codes_downloaded = false;
        $user->save();
    }

    public function markDownloaded($user)
    {
        $user->two_factor_codes_downloaded = true;
        $user->save();
    }

    public function getDecryptedRecoveryCodes($user)
    {
        if (!$user->two_factor_recovery_codes) return [];
        return json_decode(decrypt($user->two_factor_recovery_codes), true);
    }

    public function markRecoveryCodeUsed($user, $usedCode)
    {
        $codes = $this->getDecryptedRecoveryCodes($user);
        $codes = array_filter($codes, fn($code) => $code !== $usedCode);

        $user->two_factor_recovery_codes = encrypt(json_encode(array_values($codes)));
        $user->save();
    }
}
