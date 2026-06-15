<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Data;

final readonly class ConsentState
{
    public bool $allDecided;

    public function __construct(
        /** @var array<string, ConsentProfile> */
        public array $profiles,
        public ?string $identifier,
    ) {
        $this->allDecided = collect($profiles)->every(fn (ConsentProfile $p): bool => $p->isDecided());
    }

    public function hasGranted(string $category): bool
    {
        return isset($this->profiles[$category]) && $this->profiles[$category]->granted === true;
    }

    public function hasGrantedOrRequired(string $category): bool
    {
        if (! isset($this->profiles[$category])) {
            return false;
        }

        $profile = $this->profiles[$category];

        return $profile->isRequired || $profile->granted === true;
    }

    public function toArray(): array
    {
        return [
            'all_decided' => $this->allDecided,
            'categories' => array_map(fn (ConsentProfile $p): array => $p->toArray(), $this->profiles),
        ];
    }
}
