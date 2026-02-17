<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TobiSchulz\LaravelRespawnHostSdk\RespawnHostServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [RespawnHostServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('respawnhost-sdk.api_key', 'test-api-key');
        $app['config']->set('respawnhost-sdk.base_url', 'https://respawnhost.com/api/v1');
        $app['config']->set('respawnhost-sdk.catalog_base_url', 'https://respawnhost.com');
    }
}
