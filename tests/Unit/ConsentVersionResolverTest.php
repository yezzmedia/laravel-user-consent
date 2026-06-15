<?php

declare(strict_types=1);

use YezzMedia\Consent\Resolvers\ConsentVersionResolver;

beforeEach(function (): void {
    $this->resolver = new ConsentVersionResolver;
});

it('returns current version from config', function (): void {
    $version = $this->resolver->currentVersion('analytics');

    expect($version)->toBe(1);
});

it('detects current version as current', function (): void {
    expect($this->resolver->isVersionCurrent('analytics', 1))->toBeTrue();
});

it('detects outdated version', function (): void {
    expect($this->resolver->isVersionCurrent('analytics', 0))->toBeFalse();
});

it('requires reconsent when version is outdated', function (): void {
    expect($this->resolver->needsReconsent('analytics', 0))->toBeTrue();
});

it('does not require reconsent when version is current', function (): void {
    expect($this->resolver->needsReconsent('analytics', 1))->toBeFalse();
});

it('requires reconsent when no stored version exists', function (): void {
    expect($this->resolver->needsReconsent('analytics', null))->toBeTrue();
});
