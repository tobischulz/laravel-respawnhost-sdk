<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\InvalidCatalogRequestException;
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\InvalidRentRequestException;
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\RespawnHostRequestException;
use TobiSchulz\LaravelRespawnHostSdk\Facades\RespawnHost;
use TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame;
use TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGamePackage;

it('sends authenticated requests to the configured base url', function (): void {
    Http::fake([
        'https://respawnhost.com/api/v1/servers*' => Http::response(['data' => []], 200),
    ]);

    $result = RespawnHost::servers()->all();

    expect($result)->toBe(['data' => []]);

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://respawnhost.com/api/v1/servers'
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->hasHeader('Accept', 'application/json');
    });
});

it('throws a dedicated exception for failed api requests', function (): void {
    Http::fake([
        '*' => Http::response(['message' => 'Forbidden'], 403),
    ]);

    expect(fn () => RespawnHost::transactions()->all())->toThrow(RespawnHostRequestException::class);
});

it('finds a server without includePanelServer query by default', function (): void {
    Http::fake([
        'https://respawnhost.com/api/v1/servers/server-uuid' => Http::response(['id' => 'server-uuid'], 200),
    ]);

    $server = RespawnHost::servers()->find('server-uuid');

    expect($server)->toBe(['id' => 'server-uuid']);

    Http::assertSent(function (Request $request): bool {
        $query = parse_url($request->url(), PHP_URL_QUERY);

        return $request->method() === 'GET'
            && $request->url() === 'https://respawnhost.com/api/v1/servers/server-uuid'
            && ($query === null || $query === '');
    });
});

it('can include panel server data when requested', function (): void {
    Http::fake([
        'https://respawnhost.com/api/v1/servers/server-uuid*' => Http::response(['id' => 'server-uuid'], 200),
    ]);

    $server = RespawnHost::servers()->find('server-uuid', true);

    expect($server)->toBe(['id' => 'server-uuid']);

    Http::assertSent(function (Request $request): bool {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
        $includePanelServer = $query['includePanelServer'] ?? null;

        return $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://respawnhost.com/api/v1/servers/server-uuid')
            && is_string($includePanelServer)
            && $includePanelServer === '1';
    });
});

it('can explicitly disable panel server data flag', function (): void {
    Http::fake([
        'https://respawnhost.com/api/v1/servers/server-uuid*' => Http::response(['id' => 'server-uuid'], 200),
    ]);

    $server = RespawnHost::servers()->find('server-uuid', false);

    expect($server)->toBe(['id' => 'server-uuid']);

    Http::assertSent(function (Request $request): bool {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
        $includePanelServer = $query['includePanelServer'] ?? null;

        return $request->method() === 'GET'
            && str_starts_with($request->url(), 'https://respawnhost.com/api/v1/servers/server-uuid')
            && is_string($includePanelServer)
            && $includePanelServer === '0';
    });
});

it('rents a server through the facade wrapper with typed parameters', function (): void {
    Http::fake([
        'https://respawnhost.com/api/v1/servers/rent' => Http::response(['success' => true], 200),
    ]);

    $response = RespawnHost::rent(
        gameShort: 'enshrouded',
        planId: 324,
        region: 'eu',
        templateId: 11,
        templateVersionId: 22,
        instanceCount: 2,
    );

    expect($response)->toBe(['success' => true]);

    Http::assertSent(function (Request $request): bool {
        $data = $request->data();

        return $request->method() === 'POST'
            && $request->url() === 'https://respawnhost.com/api/v1/servers/rent'
            && ($data['game_short'] ?? null) === 'enshrouded'
            && ($data['plan_id'] ?? null) === 324
            && ($data['region'] ?? null) === 'eu'
            && ($data['template_id'] ?? null) === 11
            && ($data['template_version_id'] ?? null) === 22
            && ($data['instance_count'] ?? null) === 2;
    });
});

it('rejects invalid rent parameters before sending the request', function (): void {
    Http::fake();

    expect(fn () => RespawnHost::rent(gameShort: 'enshrouded', planId: 1, region: 'asia'))
        ->toThrow(InvalidRentRequestException::class, 'region');

    expect(fn () => RespawnHost::rent(gameShort: 'enshrouded', planId: -1))
        ->toThrow(InvalidRentRequestException::class, 'plan_id');

    expect(fn () => RespawnHost::rent(gameShort: 'enshrouded', planId: 1, instanceCount: 0))
        ->toThrow(InvalidRentRequestException::class, 'instance_count');

    Http::assertNothingSent();
});

it('fetches public catalog games as typed models without auth', function (): void {
    config()->set('respawnhost-sdk.api_key', null);

    Http::fake([
        'https://respawnhost.com/api/games' => Http::response([
            [
                'id' => 31,
                'name' => 'V Rising',
                'short' => 'v-rising',
                'eggId' => 58,
                'topGame' => 0,
                'neededPorts' => 3,
                'portMapping' => '{}',
                'isReleased' => 1,
                'isActive' => 1,
            ],
        ], 200),
    ]);

    $games = RespawnHost::allGames();

    expect($games)->toHaveCount(1)
        ->and($games[0])->toBeInstanceOf(CatalogGame::class)
        ->and($games[0]->short)->toBe('v-rising')
        ->and($games[0]->id)->toBe(31);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://respawnhost.com/api/games'
            && $request->method() === 'GET'
            && ! $request->hasHeader('Authorization');
    });
});

it('fetches a game by short with embedded packages as typed models', function (): void {
    Http::fake([
        'https://respawnhost.com/api/games/short/v-rising' => Http::response([
            'id' => 31,
            'name' => 'V Rising',
            'short' => 'v-rising',
            'egg_id' => 58,
            'is_active' => 1,
            'is_released' => 1,
            'game_package' => [
                [
                    'id' => 383,
                    'name' => 'v-rising-1',
                    'game_id' => 31,
                    'memory' => 4096,
                    'cpu' => 0,
                    'disk' => 0,
                    'price_hourly' => '0.01666667',
                    'price_monthly' => '12.00000000',
                ],
            ],
        ], 200),
    ]);

    $game = RespawnHost::gameByShort('v-rising');

    expect($game)->toBeInstanceOf(CatalogGame::class)
        ->and($game->short)->toBe('v-rising')
        ->and($game->packages)->toHaveCount(1)
        ->and($game->packages[0])->toBeInstanceOf(CatalogGamePackage::class)
        ->and($game->packages[0]->id)->toBe(383);
});

it('fetches packages by game short as typed models', function (): void {
    Http::fake([
        'https://respawnhost.com/api/games/short/v-rising/packages' => Http::response([
            [
                'id' => 383,
                'name' => 'v-rising-1',
                'game_id' => 31,
                'memory' => 4096,
                'cpu' => 0,
                'disk' => 0,
                'price_hourly' => '0.01666667',
                'price_monthly' => '12.00000000',
                'is_popular' => false,
                'server_count' => 0,
            ],
        ], 200),
    ]);

    $packages = RespawnHost::packagesByGameShort('v-rising');

    expect($packages)->toHaveCount(1)
        ->and($packages[0])->toBeInstanceOf(CatalogGamePackage::class)
        ->and($packages[0]->gameId)->toBe(31)
        ->and($packages[0]->name)->toBe('v-rising-1');
});

it('rejects empty game short for catalog lookups', function (): void {
    Http::fake();

    expect(fn () => RespawnHost::gameByShort(''))
        ->toThrow(InvalidCatalogRequestException::class);

    expect(fn () => RespawnHost::packagesByGameShort(''))
        ->toThrow(InvalidCatalogRequestException::class);

    Http::assertNothingSent();
});
