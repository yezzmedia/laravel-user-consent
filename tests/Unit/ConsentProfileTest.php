<?php

declare(strict_types=1);

use YezzMedia\Consent\Data\ConsentProfile;

it('creates a consent profile with granted decision', function (): void {
    $profile = new ConsentProfile(
        categorySlug: 'analytics',
        label: 'Analytics',
        description: 'Usage tracking',
        isRequired: false,
        version: 1,
        granted: true,
        identifier: '1',
    );

    expect($profile->categorySlug)->toBe('analytics')
        ->and($profile->label)->toBe('Analytics')
        ->and($profile->granted)->toBeTrue()
        ->and($profile->isDecided())->toBeTrue();
});

it('creates a consent profile without decision', function (): void {
    $profile = new ConsentProfile(
        categorySlug: 'marketing',
        label: 'Marketing',
        description: 'Marketing cookies',
        isRequired: false,
        version: 2,
        granted: null,
        identifier: 'guest:abc',
    );

    expect($profile->isDecided())->toBeFalse()
        ->and($profile->granted)->toBeNull();
});

it('marks required categories as decided by default', function (): void {
    $profile = new ConsentProfile(
        categorySlug: 'necessary',
        label: 'Necessary',
        description: 'Required cookies',
        isRequired: true,
        version: 1,
        granted: null,
        identifier: '1',
    );

    expect($profile->isRequired)->toBeTrue()
        ->and($profile->isDecided())->toBeFalse()
        ->and($profile->granted)->toBeNull();
});

it('converts profile to array', function (): void {
    $profile = new ConsentProfile(
        categorySlug: 'analytics',
        label: 'Analytics',
        description: 'Usage data',
        isRequired: false,
        version: 1,
        granted: true,
        identifier: '1',
    );

    expect($profile->toArray())->toBe([
        'slug' => 'analytics',
        'label' => 'Analytics',
        'description' => 'Usage data',
        'is_required' => false,
        'version' => 1,
        'granted' => true,
    ]);
});
