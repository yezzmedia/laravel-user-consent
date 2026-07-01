<?php

declare(strict_types=1);

use YezzMedia\Consent\ConsentPlatformPackage;
use YezzMedia\Foundation\Contracts\DefinesAuditEvents;
use YezzMedia\Foundation\Contracts\DefinesInstallSteps;
use YezzMedia\Foundation\Contracts\DefinesPermissions;
use YezzMedia\Foundation\Contracts\PlatformPackage;
use YezzMedia\Foundation\Contracts\ProvidesDoctorChecks;
use YezzMedia\Foundation\Contracts\ProvidesOpsModules;
use YezzMedia\Foundation\Contracts\RegistersFeatures;
use YezzMedia\Foundation\Registry\PackageRegistry;
use YezzMedia\Foundation\Registry\PermissionRegistry;

it('implements all required foundation contracts', function (): void {
    $package = new ConsentPlatformPackage;

    expect($package)->toBeInstanceOf(PlatformPackage::class)
        ->and($package)->toBeInstanceOf(DefinesPermissions::class)
        ->and($package)->toBeInstanceOf(DefinesAuditEvents::class)
        ->and($package)->toBeInstanceOf(RegistersFeatures::class)
        ->and($package)->toBeInstanceOf(DefinesInstallSteps::class)
        ->and($package)->toBeInstanceOf(ProvidesDoctorChecks::class)
        ->and($package)->toBeInstanceOf(ProvidesOpsModules::class);
});

it('returns correct metadata', function (): void {
    $package = new ConsentPlatformPackage;
    $metadata = $package->metadata();

    expect($metadata->name)->toBe('yezzmedia/laravel-user-consent')
        ->and($metadata->vendor)->toBe('yezzmedia')
        ->and($metadata->packageClass)->toBe(ConsentPlatformPackage::class);
});

it('defines consent.manage permission', function (): void {
    $package = new ConsentPlatformPackage;
    $permissions = $package->permissionDefinitions();

    expect(collect($permissions)->contains(fn ($p) => $p->name === 'consent.manage'))->toBeTrue();
});

it('defines audit events', function (): void {
    $package = new ConsentPlatformPackage;
    $events = $package->auditEventDefinitions();

    expect($events)->not->toBeEmpty();
});

it('registers in foundation package registry', function (): void {
    $registry = app(PackageRegistry::class);
    $packages = $registry->all()->map(fn ($p) => $p->name)->all();

    expect($packages)->toContain('yezzmedia/laravel-user-consent');
});

it('registers permission in foundation permission registry', function (): void {
    $registry = app(PermissionRegistry::class);
    $all = $registry->all();

    expect(collect($all)->contains(fn ($p) => $p->name === 'consent.manage'))->toBeTrue();
});

it('defines install steps', function (): void {
    $package = new ConsentPlatformPackage;
    $steps = $package->installSteps();

    expect($steps)->toHaveCount(2);
});

it('defines doctor checks', function (): void {
    $package = new ConsentPlatformPackage;
    $checks = $package->doctorChecks();

    expect($checks)->toHaveCount(2);
});

it('defines features', function (): void {
    $package = new ConsentPlatformPackage;
    $features = $package->featureDefinitions();

    expect($features)->toHaveCount(4);
});

it('defines ops modules', function (): void {
    $package = new ConsentPlatformPackage;
    $modules = $package->opsModuleDefinitions();

    expect($modules)->toHaveCount(1);
    expect($modules[0]->key)->toBe('consent.decisions');
    expect($modules[0]->permissionHint)->toBe('consent.manage');
});
