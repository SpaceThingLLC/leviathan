<?php

declare(strict_types=1);

namespace Leviathan\Tests;

use Leviathan\LeviathanServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LeviathanServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        // Setup default configuration
        $app['config']->set('leviathan.api_key', 'test-api-key');
        $app['config']->set('leviathan.api_secret', 'test-api-secret');
        $app['config']->set('leviathan.api_url', 'https://api.kraken.io/v1');
        $app['config']->set('leviathan.timeout', 30);
        $app['config']->set('leviathan.retry_attempts', 3);
    }
}
