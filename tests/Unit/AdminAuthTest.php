<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Security\AdminAuth;
use HaloSec\Security\LoginThrottle;
use PHPUnit\Framework\TestCase;

final class AdminAuthTest extends TestCase
{
    public function testLoginSuccessAndFailure(): void
    {
        $session = [];
        $auth = new AdminAuth($session, 'admin', password_hash('s3cret-pass', PASSWORD_DEFAULT));

        $this->assertFalse($auth->attempt('admin', 'wrong'));
        $this->assertFalse($auth->attempt('other', 's3cret-pass'));
        $this->assertFalse($auth->check());
        $this->assertTrue($auth->attempt('admin', 's3cret-pass'));
        $this->assertTrue($auth->check());
    }

    public function testUnconfiguredCredentialsNeverAuthenticate(): void
    {
        $session = [];
        $auth = new AdminAuth($session, '', '');

        $this->assertFalse($auth->attempt('', ''));
        $this->assertFalse($auth->check());
    }

    public function testIdleTimeoutAndLogout(): void
    {
        $session = [];
        $auth = new AdminAuth($session, 'admin', password_hash('pw', PASSWORD_DEFAULT));
        $auth->attempt('admin', 'pw');

        $this->assertFalse($auth->check(time() + 4000));
        $auth->attempt('admin', 'pw');
        $auth->logout();
        $this->assertSame([], $session);
        $this->assertFalse($auth->check());
    }

    public function testThrottleLocksAfterMaxFailuresAndExpires(): void
    {
        $dir = sys_get_temp_dir() . '/halosec_auth_' . bin2hex(random_bytes(4));
        $throttle = new LoginThrottle($dir, 3, 60);

        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($throttle->isLocked('1.2.3.4', 100));
            $throttle->recordFailure('1.2.3.4', 100);
        }
        $this->assertTrue($throttle->isLocked('1.2.3.4', 101));
        $this->assertFalse($throttle->isLocked('5.6.7.8', 101));
        $this->assertFalse($throttle->isLocked('1.2.3.4', 200));

        $throttle->clear('1.2.3.4');
        foreach (glob($dir . '/*') ?: [] as $f) {
            unlink($f);
        }
        rmdir($dir);
    }
}
