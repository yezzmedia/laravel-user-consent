<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use YezzMedia\Consent\Tests\ConsentTestCase;
use YezzMedia\Dashboard\DashboardServiceProvider;
use YezzMedia\Dashboard\Navigation\BottomBarLinkRegistry;

final class BottomBarLinkInjectionTest extends ConsentTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            DashboardServiceProvider::class,
        ];
    }

    #[Test]
    public function injects_cookie_settings_bottom_bar_link(): void
    {
        $registry = $this->app->make(BottomBarLinkRegistry::class);
        $config = $registry->toConfigArray();

        $this->assertArrayHasKey('left', $config);
        $this->assertArrayHasKey('right', $config);

        $labels = array_column($config['left'], 'label');
        $this->assertContains('Cookie Settings', $labels);

        $idx = array_search('Cookie Settings', $labels);
        $this->assertNotFalse($idx);
        $this->assertStringContainsString('/consent/preferences', $config['left'][$idx]['url']);
    }

    #[Test]
    public function injects_only_one_link_in_left_section(): void
    {
        $registry = $this->app->make(BottomBarLinkRegistry::class);
        $config = $registry->toConfigArray();

        $this->assertCount(1, $config['left']);
    }

    #[Test]
    public function right_section_remains_empty(): void
    {
        $registry = $this->app->make(BottomBarLinkRegistry::class);
        $config = $registry->toConfigArray();

        $this->assertCount(0, $config['right']);
    }
}
