<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Tests\Feature;

use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use YezzMedia\Account\AccountServiceProvider;
use YezzMedia\Account\Support\AccountExtensionRegistry;
use YezzMedia\Consent\Tests\ConsentTestCase;

final class SidebarLinkInjectionTest extends ConsentTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            SchemasServiceProvider::class,
            FormsServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            AccountServiceProvider::class,
        ];
    }

    #[Test]
    public function injects_cookie_settings_sidebar_link_in_account_group(): void
    {
        $registry = $this->app->make(AccountExtensionRegistry::class);
        $links = $registry->getExternalLinks();

        $this->assertArrayHasKey('Account', $links);
        $this->assertCount(1, $links['Account']);

        $link = $links['Account'][0];
        $this->assertSame('Cookie Settings', $link['label']);
        $this->assertStringContainsString('/consent/preferences', $link['url']);
        $this->assertSame('shield', $link['icon']);
        $this->assertSame(60, $link['sort']);
    }
}
