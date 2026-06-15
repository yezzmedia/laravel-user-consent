<?php

declare(strict_types=1);

use YezzMedia\Consent\Models\ConsentDecision;
use YezzMedia\Consent\Resolvers\ConsentVersionResolver;
use YezzMedia\Consent\Support\ConsentManager;

it('requires reconsent when category version changes', function (): void {
    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity(1, null);

    $manager->grant('analytics');
    expect($manager->hasGranted('analytics'))->toBeTrue();

    config(['user-consent.categories.analytics.version' => 2]);

    $resolver = $this->app->make(ConsentVersionResolver::class);
    expect($resolver->needsReconsent('analytics', 1))->toBeTrue();
});

it('does not require reconsent when version matches', function (): void {
    $resolver = $this->app->make(ConsentVersionResolver::class);

    expect($resolver->needsReconsent('analytics', 1))->toBeFalse();
});

it('stores version with decision', function (): void {
    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity(1, null);

    $manager->grant('analytics');

    $decision = ConsentDecision::where('category_slug', 'analytics')
        ->where('user_id', 1)
        ->first();

    expect($decision->version)->toBe(1);
});

it('stores updated version after reconsent with new version', function (): void {
    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity(1, null);

    config(['user-consent.categories.analytics.version' => 2]);

    $manager->grant('analytics');

    $decision = ConsentDecision::where('category_slug', 'analytics')
        ->where('user_id', 1)
        ->latest('consented_at')
        ->first();

    expect($decision->version)->toBe(2);
});
