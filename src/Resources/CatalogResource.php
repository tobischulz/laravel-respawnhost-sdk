<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Resources;

use TobiSchulz\LaravelRespawnHostSdk\Exceptions\InvalidCatalogRequestException;
use TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame;
use TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGamePackage;
use TobiSchulz\LaravelRespawnHostSdk\RespawnHost;

class CatalogResource
{
    public function __construct(protected RespawnHost $client) {}

    /**
     * @return list<CatalogGame>
     */
    public function allGames(): array
    {
        $response = $this->client->publicRequest('GET', '/api/games', baseUrl: $this->client->catalogBaseUrl());

        return $this->mapGames($response);
    }

    public function gameByShort(string $gameShort): CatalogGame
    {
        $normalizedShort = $this->normalizeShort($gameShort);

        /** @var array<string, mixed> $response */
        $response = $this->client->publicRequest(
            'GET',
            "/api/games/short/{$normalizedShort}",
            baseUrl: $this->client->catalogBaseUrl(),
        );

        return CatalogGame::fromArray($response);
    }

    /**
     * @return list<CatalogGamePackage>
     */
    public function packagesByGameShort(string $gameShort): array
    {
        $normalizedShort = $this->normalizeShort($gameShort);
        $response = $this->client->publicRequest(
            'GET',
            "/api/games/short/{$normalizedShort}/packages",
            baseUrl: $this->client->catalogBaseUrl(),
        );

        return $this->mapPackages($response);
    }

    /**
     * @param  array<int|string, mixed>  $response
     * @return list<CatalogGame>
     */
    protected function mapGames(array $response): array
    {
        $games = [];

        foreach ($response as $item) {
            if (is_array($item)) {
                $games[] = CatalogGame::fromArray($item);
            }
        }

        return $games;
    }

    /**
     * @param  array<int|string, mixed>  $response
     * @return list<CatalogGamePackage>
     */
    protected function mapPackages(array $response): array
    {
        $packages = [];

        foreach ($response as $item) {
            if (is_array($item)) {
                $packages[] = CatalogGamePackage::fromArray($item);
            }
        }

        return $packages;
    }

    protected function normalizeShort(string $gameShort): string
    {
        $normalized = strtolower(trim($gameShort));

        if ($normalized === '') {
            throw new InvalidCatalogRequestException('The catalog parameter "gameShort" is required.');
        }

        return $normalized;
    }
}
