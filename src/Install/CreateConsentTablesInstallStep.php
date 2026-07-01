<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Install;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use YezzMedia\Foundation\Data\InstallContext;
use YezzMedia\Foundation\Install\InstallStep;
use YezzMedia\Foundation\Install\OptionalInstallStep;

final class CreateConsentTablesInstallStep implements InstallStep, OptionalInstallStep
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
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable) {
            // Migration may fail if tables from other packages already exist.
            // This is non-blocking — consent_decisions will be checked below.
        }

        if (! Schema::hasTable('consent_decisions')) {
            fwrite(
                STDERR,
                "\n  \033[33;1mWARNING\033[39;22m  consent_decisions table was not created. Run 'php artisan migrate' manually.\n\n"
            );
        }
    }

    public function isOptional(): bool
    {
        return true;
    }
}
