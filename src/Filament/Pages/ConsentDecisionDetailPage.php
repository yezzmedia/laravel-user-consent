<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use YezzMedia\Consent\Models\ConsentDecision;

final class ConsentDecisionDetailPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'consent/decisions/{decision}';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'user-consent::ops.consent-decision-detail-page';

    protected static ?string $title = 'Consent Decision';

    public ConsentDecision $decision;

    public function mount(string $decision): void
    {
        $this->decision = ConsentDecision::findOrFail((int) $decision);
    }

    public static function canAccess(): bool
    {
        return Gate::check('consent.manage');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to list')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ConsentDecisionListPage::getUrl()),
        ];
    }

    public function getTitle(): string
    {
        return 'Consent Decision #'.$this->decision->id;
    }
}
