<?php

declare(strict_types=1);

namespace HaloSec\Tests\Unit;

use HaloSec\Seo\LlmsTxt;
use HaloSec\View;
use PHPUnit\Framework\TestCase;

final class LlmsTxtTest extends TestCase
{
    public function testLinksEveryServiceAndGuideUnderATitleAndSummary(): void
    {
        $services = require __DIR__ . '/../../config/services.php';
        $guides = require __DIR__ . '/../../config/guides.php';
        $md = LlmsTxt::markdown('https://halosec.in', require __DIR__ . '/../../config/app.php', $services, $guides);

        $this->assertStringStartsWith("# HaloSec\n\n> ", $md);
        foreach (array_keys($services) as $slug) {
            $this->assertStringContainsString('](https://halosec.in/services/' . $slug . ')', $md);
        }
        foreach (array_keys($guides) as $slug) {
            $this->assertStringContainsString('](https://halosec.in/guides/' . $slug . ')', $md);
        }
        $this->assertStringContainsString('](https://halosec.in/contact)', $md);
    }

    public function testPublicFormsAreWebMcpToolsButAdminLoginIsNot(): void
    {
        $view = new View(__DIR__ . '/../../views');
        $view->share(['csrfToken' => 't', 'honeypot' => 'website']);

        $this->assertStringContainsString('toolname="report_cyber_attack"', $view->include('partials/form_open', ['formAction' => '/under-attack']));
        $this->assertStringContainsString('toolname="enquire_about_security_service"', $view->include('partials/form_open', ['formAction' => '/services/soc-setup']));
        $this->assertStringNotContainsString('toolname', $view->include('partials/form_open', ['formAction' => '/admin/login']));
    }
}
