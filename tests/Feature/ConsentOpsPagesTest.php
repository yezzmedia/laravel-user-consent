<?php

declare(strict_types=1);

use YezzMedia\Consent\Filament\ConsentOpsPlugin;
use YezzMedia\Consent\Models\ConsentDecision;

it('registers consent ops plugin with correct id', function (): void {
    $plugin = ConsentOpsPlugin::make();

    expect($plugin->getId())->toBe('consent-ops');
});

it('consent decision model persists and retrieves correctly', function (): void {
    $decision = ConsentDecision::create([
        'category_slug' => 'analytics',
        'granted' => true,
        'consented_at' => now(),
        'version' => 1,
    ]);

    $found = ConsentDecision::find($decision->id);

    expect($found)->not->toBeNull()
        ->and($found->category_slug)->toBe('analytics')
        ->and($found->granted)->toBeTrue()
        ->and($found->version)->toBe(1);
});

it('consent decision model stores guest tokens', function (): void {
    $decision = ConsentDecision::create([
        'category_slug' => 'marketing',
        'guest_token' => 'test-guest-uuid',
        'granted' => false,
        'consented_at' => now(),
        'version' => 1,
    ]);

    $found = ConsentDecision::find($decision->id);

    expect($found->guest_token)->toBe('test-guest-uuid')
        ->and($found->user_id)->toBeNull();
});
