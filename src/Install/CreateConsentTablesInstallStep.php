<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use YezzMedia\Foundation\Data\InstallContext;
use YezzMedia\Foundation\Install\InstallStep;

final class CreateConsentTablesInstallStep implements InstallStep
{
    public function key(): string
    {
        return 'create_consent_tables';
    }

    public function package(): string
    {
        return 'yezzmedia/laravel-user-consent';
    }

    public function priority(): int
    {
        return 20;
    }

    public function shouldRun(InstallContext $context): bool
    {
        return $context->refreshPublishedResources || ! Schema::hasTable('consent_decisions');
    }

    public function handle(InstallContext $context): void
    {
        Artisan::call('migrate', [
            '--path' => __DIR__.'/../../database/migrations',
            '--force' => true,
        ]);

        if (! Schema::hasTable('consent_decisions')) {
            throw new RuntimeException('consent_decisions table was not created after running migrations.');
        }
    }
}
