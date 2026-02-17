<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \TobiSchulz\LaravelRespawnHostSdk\Resources\ServersResource servers()
 * @method static \TobiSchulz\LaravelRespawnHostSdk\Resources\PaymentsResource payments()
 * @method static \TobiSchulz\LaravelRespawnHostSdk\Resources\TransactionsResource transactions()
 * @method static \TobiSchulz\LaravelRespawnHostSdk\Resources\CatalogResource catalog()
 * @method static list<\TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame> allGames()
 * @method static \TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame gameByShort(string $gameShort)
 * @method static list<\TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGamePackage> packagesByGameShort(string $gameShort)
 * @method static array<int|string, mixed> rent(string $gameShort, int $planId, string $region = 'eu', ?int $templateId = null, ?int $templateVersionId = null, int $instanceCount = 1)
 * @method static array<int|string, mixed> request(string $method, string $uri, array<string, mixed> $query = [], array<string, mixed> $payload = [], array<string, string> $headers = [])
 *
 * @see \TobiSchulz\LaravelRespawnHostSdk\RespawnHost
 */
class RespawnHost extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TobiSchulz\LaravelRespawnHostSdk\RespawnHost::class;
    }
}
