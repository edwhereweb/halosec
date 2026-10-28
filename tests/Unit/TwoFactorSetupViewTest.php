<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\Totp;
use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class TwoFactorSetupViewTest extends TestCase
{
    public function testSetupPageOffersQrCodeAndManualKeyFallback(): void
    {
        $secret = Totp::generateSecret();
        $uri = Totp::provisioningUri($secret, 'test.admin', 'HaloSec');
        $html = (new View(__DIR__ . '/../../views'))->include('pages/admin_account', [
            'username' => 'test.admin',
            'requireTwoFactor' => false,
            'hasTwoFactor' => false,
            'message' => null,
            'messageIsError' => false,
            'enrollSecret' => $secret,
            'enrollUri' => $uri,
            'csrfToken' => 'token',
        ]);

        // QR data comes from the page itself and is drawn by the self-hosted library, never a third party.
        $this->assertStringContainsString('data-otpauth="' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '"', $html);
        $this->assertStringContainsString('<script src="/js/vendor/qrcode.js', $html);
        $this->assertStringContainsString('<script src="/js/admin-2fa.js', $html);
        $this->assertDoesNotMatchRegularExpression('#<script[^>]+src="https?://#', $html);
        // Manual key is open by default so it still works without JavaScript.
        $this->assertStringContainsString('data-otpauth-fallback open', $html);
        $this->assertStringContainsString(trim(chunk_split($secret, 4, ' ')), $html);
        $this->assertFileExists(__DIR__ . '/../../public/js/vendor/qrcode.js');
    }
}
