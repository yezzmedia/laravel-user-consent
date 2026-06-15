<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Data;

final readonly class ConsentProfile
{
    public function __construct(
        public string $categorySlug,
        public string $label,
        public string $description,
        public bool $isRequired,
        public int $version,
        public ?bool $granted,
        public ?string $identifier,
    ) {}

    public function isDecided(): bool
    {
        return $this->granted !== null;
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->categorySlug,
            'label' => $this->label,
            'description' => $this->description,
            'is_required' => $this->isRequired,
            'version' => $this->version,
            'granted' => $this->granted,
        ];
    }
}
