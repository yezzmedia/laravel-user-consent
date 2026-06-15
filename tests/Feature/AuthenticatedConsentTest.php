<?php

declare(strict_types=1);

use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;

it('persists decisions across requests for authenticated users', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $this->postJson('/__consent/grant-all');

    $response = $this->getJson('/__consent/state');

    $response->assertOk()
        ->assertJson(['all_decided' => true]);
});

it('respects required categories as always granted', function (): void {
    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity(1, null);

    expect($manager->hasGrantedOrRequired('necessary'))->toBeTrue();
});

it('requires explicit consent for non-required categories', function (): void {
    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity(1, null);

    expect($manager->hasGranted('analytics'))->toBeFalse();
});

it('allows reconsent by revoking then granting', function (): void {
    $user = TestConsentUser::factory()->create();

    $manager = $this->app->make(ConsentManager::class);
    $manager->usingIdentity($user->id, null);

    $manager->grant('analytics');
    expect($manager->hasGranted('analytics'))->toBeTrue();

    sleep(1);
    $manager->revoke('analytics');
    expect($manager->hasGranted('analytics'))->toBeFalse();

    sleep(1);
    $manager->grant('analytics');
    expect($manager->hasGranted('analytics'))->toBeTrue();
});

it('filters non-existent categories from save', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/__consent/save', [
        'decisions' => [
            'nonexistent' => true,
        ],
    ]);

    $response->assertStatus(422);
});
