<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Doctor;

use Illuminate\Support\Facades\Config;
use YezzMedia\Foundation\Data\DoctorResult;
use YezzMedia\Foundation\Doctor\DoctorCheck;

final readonly class ConsentConfigPublishedCheck implements DoctorCheck
{
    private const KEY = 'consent_config_published';

    private const PACKAGE = 'yezzmedia/laravel-user-consent';

    public function key(): string
    {
        return self::KEY;
    }

    public function package(): string
    {
        return self::PACKAGE;
    }

    public function run(): DoctorResult
    {
        $categories = Config::get('user-consent.categories');

        if ($categories === null || ! is_array($categories) || $categories === []) {
            return $this->result(
                status: 'warning',
                message: 'user-consent config is not published or has no categories defined.',
                isBlocking: false,
            );
        }

        return $this->result(
            status: 'passed',
            message: 'user-consent config has '.count($categories).' categories defined.',
            isBlocking: false,
        );
    }

    private function result(string $status, string $message, bool $isBlocking): DoctorResult
    {
        return new DoctorResult(
            key: $this->key(),
            package: $this->package(),
            status: $status,
            message: $message,
            isBlocking: $isBlocking,
            context: null,
        );
    }
}
