<?php

declare(strict_types=1);

use YezzMedia\Consent\Data\ConsentProfile;
use YezzMedia\Consent\Data\ConsentState;

it('reports all decided when every profile has a decision', function (): void {
    $state = new ConsentState(
        profiles: [
            'necessary' => new ConsentProfile('necessary', 'Necessary', '', true, 1, true, '1'),
            'analytics' => new ConsentProfile('analytics', 'Analytics', '', false, 1, false, '1'),
        ],
        identifier: '1',
    );

    expect($state->allDecided)->toBeTrue();
});

it('reports not all decided when a profile is undecided', function (): void {
    $state = new ConsentState(
        profiles: [
            'necessary' => new ConsentProfile('necessary', 'Necessary', '', true, 1, null, '1'),
            'analytics' => new ConsentProfile('analytics', 'Analytics', '', false, 1, null, '1'),
        ],
        identifier: '1',
    );

    expect($state->allDecided)->toBeFalse();
});

it('reports hasGranted correctly', function (): void {
    $state = new ConsentState(
        profiles: [
            'analytics' => new ConsentProfile('analytics', 'Analytics', '', false, 1, true, '1'),
            'marketing' => new ConsentProfile('marketing', 'Marketing', '', false, 1, false, '1'),
        ],
        identifier: '1',
    );

    expect($state->hasGranted('analytics'))->toBeTrue()
        ->and($state->hasGranted('marketing'))->toBeFalse()
        ->and($state->hasGranted('nonexistent'))->toBeFalse();
});

it('reports hasGrantedOrRequired correctly', function (): void {
    $state = new ConsentState(
        profiles: [
            'necessary' => new ConsentProfile('necessary', 'Necessary', '', true, 1, null, '1'),
            'analytics' => new ConsentProfile('analytics', 'Analytics', '', false, 1, true, '1'),
            'marketing' => new ConsentProfile('marketing', 'Marketing', '', false, 1, false, '1'),
        ],
        identifier: '1',
    );

    expect($state->hasGrantedOrRequired('necessary'))->toBeTrue()
        ->and($state->hasGrantedOrRequired('analytics'))->toBeTrue()
        ->and($state->hasGrantedOrRequired('marketing'))->toBeFalse();
});

it('converts consent state to array', function (): void {
    $state = new ConsentState(
        profiles: [
            'analytics' => new ConsentProfile('analytics', 'Analytics', '', false, 1, true, '1'),
        ],
        identifier: '1',
    );

    $array = $state->toArray();

    expect($array)->toHaveKey('all_decided')
        ->and($array)->toHaveKey('categories')
        ->and($array['all_decided'])->toBeTrue();

    $slugs = array_map(fn ($c) => $c['slug'], $array['categories']);

    expect($slugs)->toContain('analytics');
});
