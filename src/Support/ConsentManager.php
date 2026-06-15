<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use YezzMedia\Consent\Data\ConsentProfile;
use YezzMedia\Consent\Data\ConsentState;
use YezzMedia\Consent\Events\ConsentGranted;
use YezzMedia\Consent\Events\ConsentRevoked;
use YezzMedia\Consent\Models\ConsentDecision;
use YezzMedia\Consent\Resolvers\ConsentVersionResolver;

final class ConsentManager
{
    private ?string $resolvedGuestToken = null;

    private ?int $resolvedUserId = null;

    private ?ConsentState $cachedState = null;

    public function __construct(
        private readonly ConsentVersionResolver $versionResolver,
    ) {}

    public function resolveIdentifier(?string $guestToken = null): ?string
    {
        if ($this->resolvedUserId !== null) {
            return (string) $this->resolvedUserId;
        }

        if ($guestToken !== null) {
            $this->resolvedGuestToken = $guestToken;

            return 'guest:'.$guestToken;
        }

        $token = $this->resolvedGuestToken ?? Str::uuid()->toString();
        $this->resolvedGuestToken = $token;

        return 'guest:'.$token;
    }

    public function resolveGuestToken(): ?string
    {
        return $this->resolvedGuestToken;
    }

    public function usingIdentity(?int $userId, ?string $guestToken): void
    {
        $this->resolvedUserId = $userId;
        $this->resolvedGuestToken = $guestToken;
        $this->cachedState = null;
    }

    public function state(): ConsentState
    {
        if ($this->cachedState !== null) {
            return $this->cachedState;
        }

        $profiles = [];

        foreach ($this->getCategories() as $slug => $config) {
            $decision = $this->findDecision($slug);

            $granted = $this->resolveGranted($config, $decision);
            $needsReconsent = $decision !== null
                ? $this->versionResolver->needsReconsent($slug, $decision->version)
                : false;

            if ($needsReconsent) {
                $granted = null;
            }

            $profiles[$slug] = new ConsentProfile(
                categorySlug: $slug,
                label: $this->translate($config['label'] ?? $slug),
                description: $this->translate($config['description'] ?? ''),
                isRequired: $config['is_required'] ?? false,
                version: $this->versionResolver->currentVersion($slug),
                granted: $granted,
                identifier: $this->resolvedUserId !== null ? (string) $this->resolvedUserId : $this->resolvedGuestToken,
            );
        }

        $this->cachedState = new ConsentState(
            profiles: $profiles,
            identifier: $this->resolvedUserId !== null ? (string) $this->resolvedUserId : $this->resolvedGuestToken,
        );

        return $this->cachedState;
    }

    public function hasGranted(string $category): bool
    {
        return $this->state()->hasGranted($category);
    }

    public function hasGrantedOrRequired(string $category): bool
    {
        return $this->state()->hasGrantedOrRequired($category);
    }

    public function allDecided(): bool
    {
        return $this->state()->allDecided;
    }

    public function grant(string $category): void
    {
        $config = $this->getCategoryConfig($category);
        $version = $this->versionResolver->currentVersion($category);

        $this->storeDecision($category, true, $version);

        $this->cachedState = null;

        event(new ConsentGranted(
            category: $category,
            userId: $this->resolvedUserId,
            guestToken: $this->resolvedGuestToken,
            version: $version,
        ));
    }

    public function revoke(string $category): void
    {
        $config = $this->getCategoryConfig($category);
        $version = $this->versionResolver->currentVersion($category);

        $this->storeDecision($category, false, $version);

        $this->cachedState = null;

        event(new ConsentRevoked(
            category: $category,
            userId: $this->resolvedUserId,
            guestToken: $this->resolvedGuestToken,
            version: $version,
        ));
    }

    public function grantAll(): void
    {
        foreach ($this->getCategories() as $slug => $config) {
            $version = $this->versionResolver->currentVersion($slug);
            $this->storeDecision($slug, true, $version);
        }

        $this->cachedState = null;

        event(new ConsentGranted(
            category: '*',
            userId: $this->resolvedUserId,
            guestToken: $this->resolvedGuestToken,
            version: 0,
        ));
    }

    public function revokeAll(): void
    {
        foreach ($this->getCategories() as $slug => $config) {
            if (! ($config['is_required'] ?? false)) {
                $version = $this->versionResolver->currentVersion($slug);
                $this->storeDecision($slug, false, $version);
            }
        }

        $this->cachedState = null;

        event(new ConsentRevoked(
            category: '*',
            userId: $this->resolvedUserId,
            guestToken: $this->resolvedGuestToken,
            version: 0,
        ));
    }

    public function saveDecisions(array $decisions): void
    {
        foreach ($decisions as $slug => $granted) {
            $config = $this->getCategoryConfig($slug);
            $version = $this->versionResolver->currentVersion($slug);
            $this->storeDecision($slug, (bool) $granted, $version);
        }

        $this->cachedState = null;
    }

    public function resetAllDecisions(): void
    {
        $query = ConsentDecision::query();

        if ($this->resolvedUserId !== null) {
            $query->where('user_id', $this->resolvedUserId);
        } elseif ($this->resolvedGuestToken !== null) {
            $query->where('guest_token', $this->resolvedGuestToken);
        }

        $query->delete();
        $this->cachedState = null;
    }

    public function migrateGuestDecisions(string $guestToken, int $userId): void
    {
        ConsentDecision::where('guest_token', $guestToken)
            ->whereNull('user_id')
            ->update(['user_id' => $userId, 'guest_token' => null]);

        $this->cachedState = null;
    }

    /** @return array<string, array> */
    public function getCategories(): array
    {
        return Config::array('user-consent.categories', []);
    }

    private function getCategoryConfig(string $slug): array
    {
        $categories = $this->getCategories();

        if (! isset($categories[$slug])) {
            throw new \InvalidArgumentException("Unknown consent category: {$slug}");
        }

        return $categories[$slug];
    }

    private function findDecision(string $slug): ?ConsentDecision
    {
        $query = ConsentDecision::where('category_slug', $slug)
            ->latest('consented_at');

        if ($this->resolvedUserId !== null) {
            $query->where('user_id', $this->resolvedUserId);
        } elseif ($this->resolvedGuestToken !== null) {
            $query->where('guest_token', $this->resolvedGuestToken);
        } else {
            return null;
        }

        return $query->first();
    }

    private function storeDecision(string $category, bool $granted, int $version): void
    {
        $data = [
            'category_slug' => $category,
            'granted' => $granted,
            'consented_at' => now(),
            'version' => $version,
        ];

        if ($this->resolvedUserId !== null) {
            $data['user_id'] = $this->resolvedUserId;
        } elseif ($this->resolvedGuestToken !== null) {
            $data['guest_token'] = $this->resolvedGuestToken;
        }

        ConsentDecision::create($data);
    }

    private function resolveGranted(array $config, ?ConsentDecision $decision): ?bool
    {
        if ($decision === null) {
            if ($config['is_required'] ?? false) {
                return true;
            }

            return null;
        }

        return $decision->granted;
    }

    private function translate(array|string $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        $locale = app()->getLocale();

        return $value[$locale] ?? $value['en'] ?? (string) array_values($value)[0];
    }
}
