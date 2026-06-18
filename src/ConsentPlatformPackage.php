<?php

declare(strict_types=1);

namespace YezzMedia\Consent;

use YezzMedia\Consent\Doctor\ConsentConfigPublishedCheck;
use YezzMedia\Consent\Doctor\ConsentSchemaReadyCheck;
use YezzMedia\Consent\Install\CreateConsentTablesInstallStep;
use YezzMedia\Consent\Install\PublishConsentConfigInstallStep;
use YezzMedia\Foundation\Contracts\DefinesAuditEvents;
use YezzMedia\Foundation\Contracts\DefinesInstallSteps;
use YezzMedia\Foundation\Contracts\DefinesPermissions;
use YezzMedia\Foundation\Contracts\PlatformPackage;
use YezzMedia\Foundation\Contracts\ProvidesDoctorChecks;
use YezzMedia\Foundation\Contracts\ProvidesOpsModules;
use YezzMedia\Foundation\Contracts\RegistersFeatures;
use YezzMedia\Foundation\Data\AuditEventDefinition;
use YezzMedia\Foundation\Data\FeatureDefinition;
use YezzMedia\Foundation\Data\OpsModuleDefinition;
use YezzMedia\Foundation\Data\PackageMetadata;
use YezzMedia\Foundation\Data\PermissionDefinition;
use YezzMedia\Foundation\Doctor\DoctorCheck;
use YezzMedia\Foundation\Install\InstallStep;

final class ConsentPlatformPackage implements DefinesAuditEvents, DefinesInstallSteps, DefinesPermissions, PlatformPackage, ProvidesDoctorChecks, ProvidesOpsModules, RegistersFeatures
{
    public function metadata(): PackageMetadata
    {
        return new PackageMetadata(
            name: 'yezzmedia/laravel-user-consent',
            vendor: 'yezzmedia',
            description: 'Customer-facing consent decisions and consent-aware gating for the Yezz Media Laravel website platform.',
            packageClass: self::class,
        );
    }

    /**
     * @return array<int, PermissionDefinition>
     */
    public function permissionDefinitions(): array
    {
        return [
            new PermissionDefinition(
                name: 'consent.manage',
                package: 'yezzmedia/laravel-user-consent',
                label: 'Manage consent categories',
                description: 'Allows managing consent categories, versions, and reviewing stored decisions.',
            ),
        ];
    }

    /**
     * @return array<int, FeatureDefinition>
     */
    public function featureDefinitions(): array
    {
        return [
            new FeatureDefinition('consent.categories', 'yezzmedia/laravel-user-consent', 'Consent categories', 'Provides config-based consent category definitions with versioning support.'),
            new FeatureDefinition('consent.decisions', 'yezzmedia/laravel-user-consent', 'Consent decisions', 'Provides persistent storage for user and guest consent decisions.'),
            new FeatureDefinition('consent.banner', 'yezzmedia/laravel-user-consent', 'Consent banner', 'Provides the automatic consent banner injected into HTML responses.'),
            new FeatureDefinition('consent.preferences', 'yezzmedia/laravel-user-consent', 'Consent preferences page', 'Provides the standalone cookie preferences page at /consent/preferences.'),
        ];
    }

    /**
     * @return array<int, OpsModuleDefinition>
     */
    public function opsModuleDefinitions(): array
    {
        return [
            new OpsModuleDefinition(
                key: 'consent.decisions',
                package: 'yezzmedia/laravel-user-consent',
                label: 'Consent Decisions',
                type: 'page',
                permissionHint: 'consent.manage',
            ),
        ];
    }

    /**
     * @return array<int, AuditEventDefinition>
     */
    public function auditEventDefinitions(): array
    {
        return [
            new AuditEventDefinition(
                key: 'consent.bulk_granted',
                package: 'yezzmedia/laravel-user-consent',
                action: 'granted',
                subjectType: 'consent_decisions',
                description: 'All consent categories were granted by a user or guest.',
                severity: 'info',
                contextKeys: ['user_id', 'guest_token', 'source'],
            ),
            new AuditEventDefinition(
                key: 'consent.bulk_revoked',
                package: 'yezzmedia/laravel-user-consent',
                action: 'revoked',
                subjectType: 'consent_decisions',
                description: 'All non-required consent categories were revoked by a user or guest.',
                severity: 'info',
                contextKeys: ['user_id', 'guest_token', 'source'],
            ),
            new AuditEventDefinition(
                key: 'consent.decisions_saved',
                package: 'yezzmedia/laravel-user-consent',
                action: 'saved',
                subjectType: 'consent_decisions',
                description: 'Individual consent decisions were saved by a user or guest.',
                severity: 'info',
                contextKeys: ['user_id', 'guest_token', 'categories', 'source'],
            ),
        ];
    }

    /**
     * @return array<int, InstallStep>
     */
    public function installSteps(): array
    {
        return [
            app(PublishConsentConfigInstallStep::class),
            app(CreateConsentTablesInstallStep::class),
        ];
    }

    /**
     * @return array<int, DoctorCheck>
     */
    public function doctorChecks(): array
    {
        return [
            app(ConsentSchemaReadyCheck::class),
            app(ConsentConfigPublishedCheck::class),
        ];
    }
}
