<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Resources;

use TobiSchulz\LaravelRespawnHostSdk\Requests\ServerRentRequest;
use TobiSchulz\LaravelRespawnHostSdk\RespawnHost;

class ServersResource
{
    public function __construct(protected RespawnHost $client) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function all(array $query = []): array
    {
        return $this->client->request('GET', '/servers', query: $query);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function find(string $uuid): array
    {
        return $this->client->request('GET', "/servers/{$uuid}");
    }

    /**
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
        $request = new ServerRentRequest(
            gameShort: $gameShort,
            planId: $planId,
            region: $region,
            templateId: $templateId,
            templateVersionId: $templateVersionId,
            instanceCount: $instanceCount,
        );

        return $this->client->request('POST', '/servers/rent', payload: $request->toPayload());
    }

    /**
     * @return array<int|string, mixed>
     */
    public function delete(string $uuid): array
    {
        return $this->client->request('DELETE', "/servers/{$uuid}");
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public function sendCommand(string $uuid, array $payload): array
    {
        return $this->client->request('POST', "/servers/{$uuid}/send-command", payload: $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public function powerState(string $uuid, array $payload): array
    {
        return $this->client->request('POST', "/servers/{$uuid}/powerstate", payload: $payload);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function start(string $uuid): array
    {
        return $this->powerState($uuid, ['power_state' => 'start']);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function stop(string $uuid): array
    {
        return $this->powerState($uuid, ['power_state' => 'stop']);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function restart(string $uuid): array
    {
        return $this->powerState($uuid, ['power_state' => 'restart']);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function kill(string $uuid): array
    {
        return $this->powerState($uuid, ['power_state' => 'kill']);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public function reinstall(string $uuid, array $payload = []): array
    {
        return $this->client->request('POST', "/servers/{$uuid}/reinstall", payload: $payload);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function resourceUtilization(string $uuid): array
    {
        return $this->client->request('GET', "/servers/{$uuid}/resource-utilization");
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function transactions(string $uuid, array $query = []): array
    {
        return $this->client->request('GET', "/servers/{$uuid}/transactions", query: $query);
    }

    /**
     * Use this when a server endpoint is not wrapped yet.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $payload
     * @return array<int|string, mixed>
     */
    public function raw(
        string $uuid,
        string $method,
        string $suffix,
        array $query = [],
        array $payload = [],
    ): array {
        $suffix = ltrim($suffix, '/');

        return $this->client->request($method, "/servers/{$uuid}/{$suffix}", $query, $payload);
    }
}
