<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class ConsentRevoked
{
    use Dispatchable;

    public function __construct(
        public readonly string $category,
        public readonly ?int $userId,
        public readonly ?string $guestToken,
        public readonly int $version,
    ) {}
}
