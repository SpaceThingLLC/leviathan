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
            /** @var string $apiKey */
            $apiKey = config('leviathan.api_key');
            /** @var string $apiSecret */
            $apiSecret = config('leviathan.api_secret');
            /** @var string $apiUrl */
            $apiUrl = config('leviathan.api_url');
            /** @var int $timeout */
            $timeout = config('leviathan.timeout');
            /** @var int $retryAttempts */
            $retryAttempts = config('leviathan.retry_attempts');

            return new KrakenClient(
                apiKey: $apiKey,
                apiSecret: $apiSecret,
                apiUrl: $apiUrl,
                timeout: $timeout,
                retryAttempts: $retryAttempts
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
