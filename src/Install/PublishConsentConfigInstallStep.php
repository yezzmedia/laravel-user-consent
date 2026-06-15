<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Install;

use Illuminate\Support\Facades\Artisan;
use Throwable;
use YezzMedia\Consent\ConsentServiceProvider;
use YezzMedia\Foundation\Data\InstallContext;
use YezzMedia\Foundation\Install\InstallStep;

final class PublishConsentConfigInstallStep implements InstallStep
{
    public function key(): string
    {
        return 'publish_consent_config';
    }

    public function package(): string
    {
        return 'yezzmedia/laravel-user-consent';
    }

    public function priority(): int
    {
        return 10;
    }

    public function shouldRun(InstallContext $context): bool
    {
        return $context->refreshPublishedResources || ! class_exists(ConsentServiceProvider::class);
    }

    public function handle(InstallContext $context): void
    {
        try {
            Artisan::call('vendor:publish', [
                '--tag' => 'user-consent-config',
                '--force' => true,
            ]);
        } catch (Throwable) {
            // Config publishing is best-effort in install context
        }
    }
}
