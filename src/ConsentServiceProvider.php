<?php

declare(strict_types=1);

namespace YezzMedia\Consent;

use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use YezzMedia\Consent\Http\Middleware\ConsentBannerMiddleware;
use YezzMedia\Consent\Resolvers\ConsentVersionResolver;
use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Foundation\Support\PlatformPackageRegistrar;

class ConsentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-user-consent')
            ->hasConfigFile('user-consent')
            ->hasMigration('0001_create_consent_decisions_table')
            ->runsMigrations()
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes('web');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ConsentVersionResolver::class);
        $this->app->singleton(ConsentManager::class, fn ($app) => new ConsentManager(
            $app->make(ConsentVersionResolver::class),
        ));
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../config/user-consent.php') => config_path('user-consent.php'),
            ], 'user-consent-config');

            $this->publishes([
                $this->package->basePath('/../resources/lang') => $this->app->langPath('vendor/user-consent'),
            ], 'user-consent-translations');
        }

        $this->registerMiddleware();
        $this->registerNavigation();
        $this->registerFoundation();
    }

    private function registerMiddleware(): void
    {
        if (! config('user-consent.enabled', true)) {
            return;
        }

        $this->app->afterResolving(HttpKernelContract::class, function ($kernel, $app): void {
            /** @var Router $router */
            $router = $app->make(Router::class);

            $router->pushMiddlewareToGroup('web', ConsentBannerMiddleware::class);
        });

        $this->app->booted(function (): void {
            $this->addMiddlewareToSessionRoutes();
        });

        $this->addMiddlewareToSessionRoutes();
    }

    private function addMiddlewareToSessionRoutes(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        foreach ($router->getRoutes()->getRoutes() as $route) {
            $actionMiddleware = (array) $route->middleware();

            if (in_array(ConsentBannerMiddleware::class, $actionMiddleware, true)) {
                continue;
            }

            $route->middleware(ConsentBannerMiddleware::class);
        }
    }

    private function registerNavigation(): void
    {
        $this->app->booted(function (): void {
            $this->registerSidebarLink();
            $this->registerBottomBarLink();
        });
    }

    private function registerSidebarLink(): void
    {
        try {
            config([
                'account.navigation.extras' => [
                    'Account' => [
                        [
                            'label' => __('user-consent::messages.settings'),
                            'icon' => 'shield',
                            'url' => url('/consent/preferences'),
                            'sort' => 60,
                        ],
                    ],
                ],
            ]);
        } catch (\Throwable) {
            // account sidebar integration is optional
        }
    }

    private function registerBottomBarLink(): void
    {
        try {
            $links = (array) config('dashboard.legal.left', []);
            $links[] = [
                'label' => __('user-consent::messages.settings'),
                'url' => url('/consent/preferences'),
            ];
            config(['dashboard.legal.left' => $links]);
        } catch (\Throwable) {
            // bottom bar integration is optional
        }
    }

    private function registerFoundation(): void
    {
        $this->app->make(PlatformPackageRegistrar::class)
            ->register(new ConsentPlatformPackage);
    }
}
