<?php

declare(strict_types=1);

use YezzMedia\Consent\Models\ConsentDecision;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;

beforeEach(function (): void {
    $this->user = TestConsentUser::factory()->create();
    $this->actingAs($this->user);
});

it('grants all categories via API', function (): void {
    $response = $this->postJson('/__consent/grant-all');

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(ConsentDecision::where('user_id', $this->user->id)->count())->toBe(3);

    foreach (['necessary', 'analytics', 'marketing'] as $slug) {
        expect(ConsentDecision::where('category_slug', $slug)->where('user_id', $this->user->id)->first())
            ->granted->toBeTrue();
    }
});

it('revokes all non-required categories via API', function (): void {
    $this->postJson('/__consent/grant-all');
    sleep(1);
    $response = $this->postJson('/__consent/revoke-all');

    $response->assertOk()
        ->assertJson(['success' => true]);

    foreach (['analytics', 'marketing'] as $slug) {
        $decision = ConsentDecision::where('category_slug', $slug)
            ->where('user_id', $this->user->id)
            ->latest('consented_at')
            ->first();

        expect($decision)->not->toBeNull()
            ->and($decision->granted)->toBeFalse();
    }
});

it('saves individual decisions via API', function (): void {
    $response = $this->postJson('/__consent/save', [
        'decisions' => [
            'analytics' => true,
            'marketing' => false,
        ],
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(
        ConsentDecision::where('category_slug', 'analytics')
            ->where('user_id', $this->user->id)
            ->first()
    )->granted->toBeTrue();

    expect(
        ConsentDecision::where('category_slug', 'marketing')
            ->where('user_id', $this->user->id)
            ->first()
    )->granted->toBeFalse();
});

it('returns current consent state via API', function (): void {
    $this->postJson('/__consent/grant-all');

    $response = $this->getJson('/__consent/state');

    $response->assertOk()
        ->assertJson([
            'all_decided' => true,
        ]);

    $data = $response->json();

    expect($data['categories'])->toHaveCount(3);
});

it('returns undecided state before any consent', function (): void {
    $response = $this->getJson('/__consent/state');

    $response->assertOk()
        ->assertJson([
            'all_decided' => false,
        ]);
});
