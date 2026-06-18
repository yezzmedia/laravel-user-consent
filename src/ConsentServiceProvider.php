<?php

declare(strict_types=1);

namespace YezzMedia\Consent;

use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use YezzMedia\Account\Support\AccountExtensionRegistry;
use YezzMedia\Consent\Http\Middleware\ConsentBannerMiddleware;
use YezzMedia\Consent\Resolvers\ConsentVersionResolver;
use YezzMedia\Consent\Support\ConsentManager;
use YezzMedia\Dashboard\Navigation\BottomBarLink;
use YezzMedia\Dashboard\Navigation\BottomBarLinkRegistry;
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
        $this->registerBottomBarLink();
        $this->registerSidebarLink();
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

    private function registerSidebarLink(): void
    {
        $this->app->make(AccountExtensionRegistry::class)->addExternalLink(
            group: 'Account',
            label: __('user-consent::messages.settings'),
            url: url('/consent/preferences'),
            icon: 'shield',
            sort: 60,
        );
    }

    private function registerBottomBarLink(): void
    {
        $registry = $this->app->make(BottomBarLinkRegistry::class);
        $registry->add(new BottomBarLink(
            label: __('user-consent::messages.settings'),
            url: url('/consent/preferences'),
            section: 'left',
            sort: 20,
        ));
    }

    private function registerFoundation(): void
    {
        $this->app->make(PlatformPackageRegistrar::class)
            ->register(new ConsentPlatformPackage);
    }
}
