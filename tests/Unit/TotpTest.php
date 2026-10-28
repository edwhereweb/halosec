<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\Totp;
use PHPUnit\Framework\TestCase;

final class TotpTest extends TestCase
{
    /** RFC 6238 appendix B test vectors (SHA1, secret "12345678901234567890"), truncated to 6 digits. */
    public function testMatchesRfcVectors(): void
    {
        $secret = Totp::base32Encode('12345678901234567890');

        $this->assertSame('287082', Totp::code($secret, intdiv(59, 30)));
        $this->assertSame('081804', Totp::code($secret, intdiv(1111111109, 30)));
        $this->assertSame('005924', Totp::code($secret, intdiv(1234567890, 30)));
    }

    public function testBase32RoundTrip(): void
    {
        $bytes = random_bytes(20);

        $this->assertSame($bytes, Totp::base32Decode(Totp::base32Encode($bytes)));
        $this->assertSame('', Totp::base32Decode('not base32!'));
    }

    public function testVerifyAllowsOneStepOfClockDriftOnly(): void
    {
        $secret = Totp::generateSecret();
        $now = 1_700_000_000;
        $step = intdiv($now, 30);

        $this->assertSame($step, Totp::verify($secret, Totp::code($secret, $step), $now));
        $this->assertSame($step - 1, Totp::verify($secret, Totp::code($secret, $step - 1), $now));
        $this->assertNull(Totp::verify($secret, Totp::code($secret, $step - 3), $now));
        $this->assertNull(Totp::verify($secret, 'abcdef', $now));
        $this->assertNull(Totp::verify('', '123456', $now));
    }
}
