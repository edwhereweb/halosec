<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class AdminLayoutTest extends TestCase
{
    public function testEmergencyBannerToggleHidesTheWholeBanner(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $html = $view->include('layout', [
            'config' => ['name' => 'HaloSec', 'emergency_phone' => '+1234567890'],
            'services' => [],
            'contentBlocks' => ['home.emergency_banner_enabled' => '0'],
            'content' => '<p>Content</p>',
        ]);

        $this->assertStringNotContainsString('SOC Monitoring 24/7 Active', $html);
        $this->assertStringContainsString('Emergency Hotline', $html);
    }
}
