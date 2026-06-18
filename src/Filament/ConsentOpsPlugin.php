<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use YezzMedia\Consent\Filament\Pages\ConsentDecisionDetailPage;
use YezzMedia\Consent\Filament\Pages\ConsentDecisionListPage;
use YezzMedia\Consent\Filament\Widgets\ConsentStatsWidget;

final class ConsentOpsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(self::class);
    }

    public function getId(): string
    {
        return 'consent-ops';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            ConsentDecisionListPage::class,
            ConsentDecisionDetailPage::class,
        ])->widgets([
            ConsentStatsWidget::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
