<?php

declare(strict_types=1);

use YezzMedia\Consent\Models\ConsentDecision;
use YezzMedia\Consent\Resolvers\ConsentVersionResolver;
use YezzMedia\Consent\Support\ConsentManager;

beforeEach(function (): void {
    $this->manager = new ConsentManager(new ConsentVersionResolver);
});

it('returns non-decided state for new identity', function (): void {
    $this->manager->usingIdentity(null, 'test-token');

    expect($this->manager->allDecided())->toBeFalse();
});

it('marks necessary categories as granted by default', function (): void {
    $this->manager->usingIdentity(null, 'test-token');

    expect($this->manager->hasGrantedOrRequired('necessary'))->toBeTrue();
});

it('grants a single category', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grant('analytics');

    expect($this->manager->hasGranted('analytics'))->toBeTrue();
});

it('revokes a single category', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grant('analytics');
    sleep(1);
    $this->manager->revoke('analytics');

    expect($this->manager->hasGranted('analytics'))->toBeFalse();
});

it('grants all categories', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grantAll();

    expect($this->manager->hasGranted('analytics'))->toBeTrue()
        ->and($this->manager->hasGranted('marketing'))->toBeTrue()
        ->and($this->manager->allDecided())->toBeTrue();
});

it('revokes all non-required categories', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grantAll();
    sleep(1);
    $this->manager->revokeAll();

    expect($this->manager->hasGranted('analytics'))->toBeFalse()
        ->and($this->manager->hasGranted('marketing'))->toBeFalse()
        ->and($this->manager->hasGrantedOrRequired('necessary'))->toBeTrue();
});

it('saves individual decisions', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->saveDecisions([
        'analytics' => true,
        'marketing' => false,
    ]);

    expect($this->manager->hasGranted('analytics'))->toBeTrue()
        ->and($this->manager->hasGranted('marketing'))->toBeFalse();
});

it('stores decision in database', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grant('analytics');

    $decision = ConsentDecision::where('category_slug', 'analytics')
        ->where('user_id', 1)
        ->first();

    expect($decision)->not->toBeNull()
        ->and($decision->granted)->toBeTrue()
        ->and($decision->version)->toBe(1);
});

it('stores guest decision with guest token', function (): void {
    $this->manager->usingIdentity(null, 'guest-token-123');

    $this->manager->grant('analytics');

    $decision = ConsentDecision::where('category_slug', 'analytics')
        ->where('guest_token', 'guest-token-123')
        ->first();

    expect($decision)->not->toBeNull()
        ->and($decision->guest_token)->toBe('guest-token-123')
        ->and($decision->user_id)->toBeNull();
});

it('migrates guest decisions to user', function (): void {
    $this->manager->usingIdentity(null, 'guest-token-123');

    $this->manager->grant('analytics');
    $this->manager->grant('marketing');

    $this->manager->migrateGuestDecisions('guest-token-123', 42);

    $this->manager->usingIdentity(42, null);

    expect($this->manager->hasGranted('analytics'))->toBeTrue()
        ->and($this->manager->hasGranted('marketing'))->toBeTrue();
});

it('throws exception for unknown category', function (): void {
    $this->manager->usingIdentity(1, null);

    expect(fn () => $this->manager->grant('nonexistent'))
        ->toThrow(InvalidArgumentException::class, 'Unknown consent category: nonexistent');
});

it('detects version changes and requires reconsent', function (): void {
    $this->manager->usingIdentity(1, null);

    $this->manager->grant('analytics');

    expect($this->manager->hasGranted('analytics'))->toBeTrue();
});
