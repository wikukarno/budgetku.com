<?php

namespace App\Services;

use App\Repositories\TwoFactorRepository;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorService
{
    protected $repo;
    protected $google2fa;

    public function __construct(TwoFactorRepository $repo)
    {
        $this->repo = $repo;
        $this->google2fa = new Google2FA();
    }

    public function setup2FA($user)
    {
        $secret = $this->google2fa->generateSecretKey();
        $googleUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate QR Code as data URI
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );
        $qrSvg = $writer->writeString($googleUrl);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $this->repo->storeSecretTemp($user, $secret);

        return [
            'qr_code' => $qrDataUri,
            'secret' => $secret
        ];
    }

    public function verify2FA($user, $code)
    {
        $secret = session('2fa_secret');

        if (!$this->google2fa->verifyKey($secret, $code)) {
            return ['status' => false];
        }

        $recoveryCodes = $this->repo->generateRecoveryCodes();
        $this->repo->enable2FA($user, $secret, $recoveryCodes);

        return [
            'status' => true,
            'recovery_codes' => $recoveryCodes
        ];
    }

    public function disable2FA($user)
    {
        $this->repo->disable2FA($user);
    }

    public function markCodesDownloaded($user)
    {
        $this->repo->markDownloaded($user);
    }

    public function getRecoveryCodes($user)
    {
        return $this->repo->getDecryptedRecoveryCodes($user);
    }

    public function verifyLoginCode($user, string $code): bool
    {
        try {
            $secret = decrypt($user->two_factor_secret);
        } catch (\Exception $e) {
            return false;
        }

        $google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();

        // Validasi pakai Google Authenticator
        if ($google2fa->verifyKey($secret, $code)) {
            return true;
        }

        // Cek apakah code ada di recovery codes
        $recoveryCodes = $this->repo->getDecryptedRecoveryCodes($user);

        if (in_array($code, $recoveryCodes)) {
            $this->repo->markRecoveryCodeUsed($user, $code);
            return true;
        }

        return false;
    }
}
