<?php

namespace TobiSchulz\LaravelRespawnHostSdk;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\MissingApiKeyException;
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\RespawnHostRequestException;
use TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame;
use TobiSchulz\LaravelRespawnHostSdk\Resources\CatalogResource;
use TobiSchulz\LaravelRespawnHostSdk\Resources\PaymentsResource;
use TobiSchulz\LaravelRespawnHostSdk\Resources\ServersResource;
use TobiSchulz\LaravelRespawnHostSdk\Resources\TransactionsResource;

class RespawnHost
{
    public function __construct(
        protected HttpFactory $http,
        protected ?string $apiKey,
        protected string $baseUrl,
        protected int $timeout,
        protected int $connectTimeout,
        protected int $retryTimes,
        protected int $retrySleep,
        protected string $userAgent,
        protected string $catalogBaseUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromConfig(HttpFactory $http, array $config): self
    {
        $retry = $config['retry'] ?? [];

        return new self(
            http: $http,
            apiKey: isset($config['api_key']) ? trim((string) $config['api_key']) : null,
            baseUrl: rtrim((string) ($config['base_url'] ?? 'https://respawnhost.com/api/v1'), '/'),
            timeout: max(1, (int) ($config['timeout'] ?? 30)),
            connectTimeout: max(1, (int) ($config['connect_timeout'] ?? 10)),
            retryTimes: max(1, (int) (is_array($retry) ? ($retry['times'] ?? 1) : 1)),
            retrySleep: max(0, (int) (is_array($retry) ? ($retry['sleep'] ?? 200) : 200)),
            userAgent: trim((string) ($config['user_agent'] ?? 'laravel-respawnhost-sdk')),
            catalogBaseUrl: rtrim((string) ($config['catalog_base_url'] ?? 'https://respawnhost.com'), '/'),
        );
    }

    public function servers(): ServersResource
    {
        return new ServersResource($this);
    }

    public function payments(): PaymentsResource
    {
        return new PaymentsResource($this);
    }

    public function transactions(): TransactionsResource
    {
        return new TransactionsResource($this);
    }

    public function catalog(): CatalogResource
    {
        return new CatalogResource($this);
    }

    /**
     * Convenience wrapper to list public catalog games.
     *
     * @return list<CatalogGame>
     */
    public function allGames(): array
    {
        return $this->catalog()->allGames();
    }

    public function gameByShort(string $gameShort): CatalogGame
    {
        return $this->catalog()->gameByShort($gameShort);
    }

    /**
     * @return list<\TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGamePackage>
     */
    public function packagesByGameShort(string $gameShort): array
    {
        return $this->catalog()->packagesByGameShort($gameShort);
    }

    /**
     * Convenience wrapper to rent a server via the facade.
     *
     * @return array<int|string, mixed>
     */
    public function rent(
        string $gameShort,
        int $planId,
        string $region = 'eu',
        ?int $templateId = null,
        ?int $templateVersionId = null,
        int $instanceCount = 1,
    ): array {
        return $this->servers()->rent(
            gameShort: $gameShort,
            planId: $planId,
            region: $region,
            templateId: $templateId,
            templateVersionId: $templateVersionId,
            instanceCount: $instanceCount,
        );
    }

    public function catalogBaseUrl(): string
    {
        return $this->catalogBaseUrl;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     * @return array<int|string, mixed>
     */
    public function request(
        string $method,
        string $uri,
        array $query = [],
        array $payload = [],
        array $headers = [],
    ): array {
        /** @var array<int|string, mixed>|null $decoded */
        $decoded = $this->send($method, $uri, $query, $payload, $headers)->json();

        return $decoded ?? [];
    }

    /**
     * Send public catalog requests without requiring an API key.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     * @return array<int|string, mixed>
     */
    public function publicRequest(
        string $method,
        string $uri,
        array $query = [],
        array $payload = [],
        array $headers = [],
        ?string $baseUrl = null,
    ): array {
        /** @var array<int|string, mixed>|null $decoded */
        $decoded = $this->sendPublic($method, $uri, $query, $payload, $headers, $baseUrl)->json();

        return $decoded ?? [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function send(
        string $method,
        string $uri,
        array $query = [],
        array $payload = [],
        array $headers = [],
    ): Response {
        $options = [];

        if ($query !== []) {
            $options['query'] = $query;
        }

        if ($payload !== []) {
            $options['json'] = $payload;
        }

        $request = $this->newRequest();

        if ($headers !== []) {
            $request = $request->withHeaders($headers);
        }

        $response = $request->send(strtoupper($method), ltrim($uri, '/'), $options);

        if ($response->failed()) {
            throw RespawnHostRequestException::fromResponse($response);
        }

        return $response;
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function sendPublic(
        string $method,
        string $uri,
        array $query = [],
        array $payload = [],
        array $headers = [],
        ?string $baseUrl = null,
    ): Response {
        $options = [];

        if ($query !== []) {
            $options['query'] = $query;
        }

        if ($payload !== []) {
            $options['json'] = $payload;
        }

        $request = $this->newPublicRequest($baseUrl);

        if ($headers !== []) {
            $request = $request->withHeaders($headers);
        }

        $response = $request->send(strtoupper($method), ltrim($uri, '/'), $options);

        if ($response->failed()) {
            throw RespawnHostRequestException::fromResponse($response);
        }

        return $response;
    }

    public function newRequest(): PendingRequest
    {
        $apiKey = $this->apiKey;

        if ($apiKey === null || $apiKey === '') {
            throw new MissingApiKeyException('RespawnHost API key is missing. Set RESPAWNHOST_API_KEY in your environment.');
        }

        $request = $this->http
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->withToken($apiKey)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);

        if ($this->userAgent !== '') {
            $request = $request->withUserAgent($this->userAgent);
        }

        if ($this->retryTimes > 1) {
            $request = $request->retry($this->retryTimes, $this->retrySleep);
        }

        return $request;
    }

    public function newPublicRequest(?string $baseUrl = null): PendingRequest
    {
        $resolvedBaseUrl = $baseUrl === null || $baseUrl === ''
            ? $this->catalogBaseUrl
            : rtrim($baseUrl, '/');

        $request = $this->http
            ->baseUrl($resolvedBaseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);

        if ($this->userAgent !== '') {
            $request = $request->withUserAgent($this->userAgent);
        }

        if ($this->retryTimes > 1) {
            $request = $request->retry($this->retryTimes, $this->retrySleep);
        }

        return $request;
    }
}
