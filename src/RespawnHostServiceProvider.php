<?php

namespace TobiSchulz\LaravelRespawnHostSdk;

use Illuminate\Http\Client\Factory as HttpFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RespawnHostServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-respawnhost-sdk')
            ->hasConfigFile('respawnhost-sdk');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(RespawnHost::class, function ($app): RespawnHost {
            /** @var array<string, mixed> $config */
            $config = $app['config']->get('respawnhost-sdk', []);

            return RespawnHost::fromConfig($app->make(HttpFactory::class), $config);
        });
    }
}
