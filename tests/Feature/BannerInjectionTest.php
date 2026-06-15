<?php

declare(strict_types=1);

use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;

beforeEach(function (): void {
    config(['user-consent.enabled' => true]);
});

it('injects consent banner into html responses', function (): void {
    $response = $this->get('/test-page');

    $response->assertStatus(200);

    $content = $response->getContent();

    expect($content)->toContain('id="consent-banner"');
});

it('does not inject banner when already decided', function (): void {
    $user = TestConsentUser::factory()->create();
    $this->actingAs($user);

    $manager = app(ConsentManager::class);
    $manager->usingIdentity($user->id, null);
    $manager->grantAll();

    $response = $this->get('/test-page');

    $response->assertStatus(200);
    $content = $response->getContent();

    expect($content)->not->toContain('id="consent-banner"');
});

it('does not inject banner into json responses', function (): void {
    $response = $this->getJson('/__consent/state');

    $response->assertOk();

    $contentType = $response->headers->get('Content-Type');

    expect($contentType)->toContain('application/json');
});

it('does not inject banner when consent disabled', function (): void {
    config(['user-consent.enabled' => false]);

    $response = $this->get('/test-page');
    $content = $response->getContent();

    expect($content)->not->toContain('id="consent-banner"');
});
