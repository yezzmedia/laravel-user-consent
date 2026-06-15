<?php

declare(strict_types=1);

use YezzMedia\Consent\ConsentServiceProvider;
use YezzMedia\Consent\Support\ConsentManager;

it('registers the service provider', function (): void {
    $providers = app()->getLoadedProviders();

    expect(isset($providers[ConsentServiceProvider::class]))->toBeTrue();
});

it('binds ConsentManager as singleton', function (): void {
    $manager1 = app(ConsentManager::class);
    $manager2 = app(ConsentManager::class);

    expect($manager1)->toBeInstanceOf(ConsentManager::class)
        ->and($manager1)->toBe($manager2);
});

it('loads config from package', function (): void {
    $categories = config('user-consent.categories');

    expect($categories)->toHaveKeys(['necessary', 'analytics', 'marketing']);
});

it('registers routes', function (): void {
    $routes = collect(app('router')->getRoutes())->map(fn ($r) => $r->uri())->all();

    expect(in_array('__consent/state', $routes))->toBeTrue()
        ->and(in_array('__consent/grant-all', $routes))->toBeTrue()
        ->and(in_array('__consent/revoke-all', $routes))->toBeTrue()
        ->and(in_array('__consent/save', $routes))->toBeTrue()
        ->and(in_array('consent/preferences', $routes))->toBeTrue();
});
