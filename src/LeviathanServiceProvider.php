<?php

declare(strict_types=1);

namespace Leviathan;

use Illuminate\Support\ServiceProvider;
use Leviathan\Client\Contracts\KrakenClientInterface;
use Leviathan\Client\KrakenClient;

class LeviathanServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/leviathan.php',
            'leviathan'
        );

        $this->app->singleton(KrakenClientInterface::class, function ($app) {
            return new KrakenClient(
                apiKey: config('leviathan.api_key'),
                apiSecret: config('leviathan.api_secret'),
                apiUrl: config('leviathan.api_url'),
                timeout: config('leviathan.timeout'),
                retryAttempts: config('leviathan.retry_attempts')
            );
        });

        $this->app->alias(KrakenClientInterface::class, 'leviathan');
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/leviathan.php' => config_path('leviathan.php'),
            ], 'leviathan-config');
        }
    }
}
