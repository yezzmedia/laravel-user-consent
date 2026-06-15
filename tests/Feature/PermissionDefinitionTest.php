<?php

declare(strict_types=1);

use YezzMedia\Consent\ConsentPlatformPackage;

it('defines consent.manage with correct package', function (): void {
    $package = new ConsentPlatformPackage;
    $permission = collect($package->permissionDefinitions())
        ->first(fn ($p) => $p->name === 'consent.manage');

    expect($permission)->not->toBeNull()
        ->and($permission->package)->toBe('yezzmedia/laravel-user-consent')
        ->and($permission->label)->toBe('Manage consent categories');
});
