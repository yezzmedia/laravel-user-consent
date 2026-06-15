<?php

declare(strict_types=1);

use YezzMedia\Consent\Models\ConsentDecision;
use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;

it('handles guest consent with cookie token via manager', function (): void {
    $manager = app(ConsentManager::class);
    $manager->usingIdentity(null, 'guest-token-abc');

    $manager->grant('analytics');
    $manager->grant('marketing');

    expect(
        ConsentDecision::where('guest_token', 'guest-token-abc')->count()
    )->toBe(2);
});

it('saves guest decisions using manager', function (): void {
    $manager = app(ConsentManager::class);
    $manager->usingIdentity(null, 'guest-token-xyz');

    $manager->saveDecisions([
        'analytics' => true,
        'marketing' => false,
    ]);

    expect(
        ConsentDecision::where('guest_token', 'guest-token-xyz')
            ->where('category_slug', 'analytics')
            ->first()
    )->granted->toBeTrue();

    expect(
        ConsentDecision::where('guest_token', 'guest-token-xyz')
            ->where('category_slug', 'marketing')
            ->first()
    )->granted->toBeFalse();
});

it('migrates guest decisions to user after login', function (): void {
    $token = 'guest-to-user-token';

    $manager = app(ConsentManager::class);
    $manager->usingIdentity(null, $token);
    $manager->grantAll();

    expect(
        ConsentDecision::where('guest_token', $token)->whereNull('user_id')->count()
    )->toBe(3);

    $user = TestConsentUser::factory()->create();

    $manager->migrateGuestDecisions($token, $user->id);

    $manager->usingIdentity($user->id, null);

    expect($manager->hasGranted('analytics'))->toBeTrue()
        ->and($manager->hasGranted('marketing'))->toBeTrue()
        ->and(ConsentDecision::where('guest_token', $token)->whereNull('user_id')->count())->toBe(0);
});
