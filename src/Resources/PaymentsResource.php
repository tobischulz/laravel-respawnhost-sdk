<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Resources;

use TobiSchulz\LaravelRespawnHostSdk\RespawnHost;

class PaymentsResource
{
    public function __construct(protected RespawnHost $client) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/payments', query: $query);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function downloadInvoice(int $paymentId): array
    {
        return $this->client->request('GET', "/payments/{$paymentId}/download");
    }
}
