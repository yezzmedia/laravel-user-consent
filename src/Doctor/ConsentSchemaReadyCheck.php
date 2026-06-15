<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Doctor;

use Illuminate\Support\Facades\Schema;
use YezzMedia\Foundation\Data\DoctorResult;
use YezzMedia\Foundation\Doctor\DoctorCheck;

final readonly class ConsentSchemaReadyCheck implements DoctorCheck
{
    private const KEY = 'consent_schema_ready';

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
        if (! Schema::hasTable('consent_decisions')) {
            return $this->result(
                status: 'failed',
                message: 'consent_decisions table is missing. Run the package migrations.',
                isBlocking: true,
            );
        }

        return $this->result(
            status: 'passed',
            message: 'consent_decisions table exists.',
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
