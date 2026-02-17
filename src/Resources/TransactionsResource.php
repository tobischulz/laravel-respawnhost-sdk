<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Resources;

use TobiSchulz\LaravelRespawnHostSdk\RespawnHost;

class TransactionsResource
{
    public function __construct(protected RespawnHost $client) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/transactions', query: $query);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function find(int $transactionId): array
    {
        return $this->client->request('GET', "/transactions/{$transactionId}");
    }
}
