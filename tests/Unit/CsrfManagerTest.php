<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\CsrfManager;
use PHPUnit\Framework\TestCase;

final class CsrfManagerTest extends TestCase
{
    public function testTokenIsStableAndValidates(): void
    {
        $session = [];
        $csrf = new CsrfManager($session);
        $token = $csrf->token();

        $this->assertSame($token, $csrf->token());
        $this->assertTrue($csrf->isValid($token));
        $this->assertFalse($csrf->isValid('wrong'));
        $this->assertFalse($csrf->isValid(null));
        $this->assertFalse($csrf->isValid(['x']));
    }

    public function testNoTokenMeansInvalid(): void
    {
        $session = [];
        $this->assertFalse((new CsrfManager($session))->isValid(''));
    }
}
