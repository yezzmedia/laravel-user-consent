<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Resolvers;

use Illuminate\Support\Facades\Config;

final readonly class ConsentVersionResolver
{
    public function currentVersion(string $category): int
    {
        return Config::integer("user-consent.categories.{$category}.version", 1);
    }

    public function isVersionCurrent(string $category, int $storedVersion): bool
    {
        return $this->currentVersion($category) === $storedVersion;
    }

    public function needsReconsent(string $category, ?int $storedVersion): bool
    {
        if ($storedVersion === null) {
            return true;
        }

        return ! $this->isVersionCurrent($category, $storedVersion);
    }
}
