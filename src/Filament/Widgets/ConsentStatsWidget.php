<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use YezzMedia\Consent\Models\ConsentDecision;

class ConsentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $total = ConsentDecision::count();
        $granted = ConsentDecision::where('granted', true)->count();
        $denied = ConsentDecision::where('granted', false)->count();
        $users = ConsentDecision::whereNotNull('user_id')->distinct('user_id')->count('user_id');

        return [
            Stat::make('Total Decisions', (string) $total)
                ->icon('heroicon-o-shield-check')
                ->color('gray'),
            Stat::make('Granted', (string) $granted)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Denied', (string) $denied)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Users', (string) $users)
                ->icon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
