<?php

declare(strict_types=1);

return [

    'enabled' => env('CONSENT_ENABLED', true),

    'categories' => [
        'necessary' => [
            'label' => ['en' => 'Necessary', 'de' => 'Notwendig'],
            'description' => [
                'en' => 'Technically required cookies and services for the basic operation of the website.',
                'de' => 'Technisch erforderliche Cookies und Dienste für den grundlegenden Betrieb der Website.',
            ],
            'is_required' => true,
            'version' => 1,
        ],
        'analytics' => [
            'label' => ['en' => 'Analytics', 'de' => 'Statistik'],
            'description' => [
                'en' => 'Helps us understand how visitors interact with the website by collecting anonymous usage data.',
                'de' => 'Hilft uns zu verstehen, wie Besucher mit der Website interagieren, durch anonyme Nutzungsdaten.',
            ],
            'is_required' => false,
            'version' => 1,
        ],
        'marketing' => [
            'label' => ['en' => 'Marketing', 'de' => 'Marketing'],
            'description' => [
                'en' => 'Enables personalized advertising, content recommendations, and social media features.',
                'de' => 'Ermöglicht personalisierte Werbung, Inhaltsempfehlungen und Social-Media-Funktionen.',
            ],
            'is_required' => false,
            'version' => 1,
        ],
    ],

    'guest_token_cookie' => 'consent_token',

    'guest_token_lifetime_days' => 365,

];
