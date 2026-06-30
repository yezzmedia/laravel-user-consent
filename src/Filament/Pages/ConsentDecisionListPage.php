<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use UnitEnum;
use YezzMedia\Consent\Models\ConsentDecision;

final class ConsentDecisionListPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Consent';

    protected static ?string $navigationLabel = 'Consent Decisions';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Consent Decisions';

    protected string $view = 'user-consent::ops.consent-decision-list-page';

    protected static ?string $slug = 'consent/decisions';

    public static function canAccess(): bool
    {
        return Gate::check('consent.manage');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Consent Decisions')
            ->description('All recorded consent decisions from users and guests.')
            ->query(ConsentDecision::query())
            ->defaultSort('consented_at', 'desc')
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(40),
                TextColumn::make('category_slug')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('granted')
                    ->label('')
                    ->width(30)
                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('granted')
                    ->label('Granted')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Granted' : 'Denied')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextColumn::make('user_id')
                    ->label('User ID')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? (string) $state : '—'),
                TextColumn::make('guest_token')
                    ->label('Guest Token')
                    ->limit(16)
                    ->searchable(),
                TextColumn::make('version')
                    ->label('Ver.')
                    ->sortable()
                    ->width(40),
                TextColumn::make('consented_at')
                    ->label('Consented At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (ConsentDecision $record): string => ConsentDecisionDetailPage::getUrl(['decision' => $record->id], panel: 'ops')),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No consent decisions yet.')
            ->emptyStateDescription('Consent decisions will appear here as users interact with the consent banner.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
