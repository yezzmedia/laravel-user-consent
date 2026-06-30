<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use YezzMedia\Account\Pages\AccountPage;
use YezzMedia\Account\Support\AccountManager;
use YezzMedia\Consent\Support\ConsentManager;

final class ConsentController extends Controller
{
    public function __construct(
        private readonly ConsentManager $consent,
    ) {}

    public function state(Request $request): JsonResponse
    {
        $this->resolveIdentity($request);

        return response()->json($this->consent->state()->toArray());
    }

    public function grantAll(Request $request): JsonResponse
    {
        $this->resolveIdentity($request);
        $this->consent->grantAll();

        return response()->json(['success' => true]);
    }

    public function revokeAll(Request $request): JsonResponse
    {
        $this->resolveIdentity($request);
        $this->consent->revokeAll();

        return response()->json(['success' => true]);
    }

    public function save(Request $request): JsonResponse
    {
        $this->resolveIdentity($request);

        $validated = $request->validate([
            'decisions' => ['required', 'array'],
            'decisions.*' => ['boolean'],
        ]);

        try {
            $this->consent->saveDecisions($validated['decisions']);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'decisions' => [$e->getMessage()],
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function preferences(Request $request): View
    {
        $this->resolveIdentity($request);
        $state = $this->consent->state();

        $categories = [];

        foreach ($state->profiles as $slug => $profile) {
            $categories[] = [
                'slug' => $slug,
                'label' => $profile->label,
                'description' => $profile->description,
                'is_required' => $profile->isRequired,
                'granted' => $profile->granted,
            ];
        }

        $title = __('user-consent::messages.preferences_title');
        $description = __('user-consent::messages.preferences_hero_text');

        if (class_exists(AccountPage::class) && $this->hasDefaultFilamentPanel()) {
            $manager = app(AccountManager::class);
            $user = $manager->currentUser();

            return view('user-consent::pages.account-preferences', [
                'title' => $title,
                'description' => $description,
                'categories' => $categories,
                'profileSummary' => $manager->profileSummary($user),
                'navigation' => $manager->navigation(),
            ]);
        }

        return view('user-consent::pages.preferences', [
            'title' => $title,
            'description' => $description,
            'categories' => $categories,
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $this->resolveIdentity($request);
        $this->consent->resetAllDecisions();

        return response()->json(['success' => true]);
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $this->resolveIdentity($request);

        $validated = $request->validate([
            'decisions' => ['required', 'array'],
            'decisions.*' => ['boolean'],
        ]);

        $this->consent->saveDecisions($validated['decisions']);

        return redirect()->back()->with('success', __('user-consent::messages.preferences_saved'));
    }

    private function hasDefaultFilamentPanel(): bool
    {
        try {
            filament()->getDefaultPanel();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function resolveIdentity(Request $request): void
    {
        $user = $request->user();
        $guestToken = $request->cookie(config('user-consent.guest_token_cookie', 'consent_token'));

        $this->consent->usingIdentity(
            userId: $user?->getAuthIdentifier(),
            guestToken: $guestToken,
        );
    }
}
