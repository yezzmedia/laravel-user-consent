<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use YezzMedia\Consent\ConsentServiceProvider;
use YezzMedia\Consent\Http\Middleware\ConsentBannerMiddleware;
use YezzMedia\Consent\Tests\Fixtures\TestConsentUser;
use YezzMedia\Foundation\Testing\FoundationTestCase;

abstract class ConsentTestCase extends FoundationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Route::get('/test-page', fn () => response('<html><body>test</body></html>', 200, ['Content-Type' => 'text/html']))
            ->middleware(ConsentBannerMiddleware::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->ensureUsersTableExists();
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            ConsentServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        $app['config']->set('app.name', 'YezzMedia');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', TestConsentUser::class);
    }

    private function ensureUsersTableExists(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
