<?php

declare(strict_types=1);

use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;

it('shows preferences page', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/consent/preferences');

    $response->assertStatus(200)
        ->assertSee('Cookie Preferences');
});

it('shows all consent categories on preferences page', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/consent/preferences');

    $response->assertStatus(200)
        ->assertSee('Necessary')
        ->assertSee('Analytics')
        ->assertSee('Marketing');
});

it('updates preferences via POST', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/consent/preferences', [
        'decisions' => [
            'analytics' => true,
            'marketing' => false,
        ],
    ]);

    $response->assertStatus(302);
});

it('shows preferences page for guests', function (): void {
    $manager = app(ConsentManager::class);
    $manager->usingIdentity(null, 'test-guest');
    $manager->grantAll();

    $response = $this->get('/consent/preferences');

    $response->assertStatus(200)
        ->assertSee('Cookie Preferences');
});

it('shows current consent state on preferences page', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $manager = app(ConsentManager::class);
    $manager->usingIdentity($user->id, null);
    $manager->grantAll();

    $response = $this->get('/consent/preferences');

    $response->assertStatus(200)
        ->assertSee('granted');
});
