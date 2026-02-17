<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Requests;

use TobiSchulz\LaravelRespawnHostSdk\Exceptions\InvalidRentRequestException;

class ServerRentRequest
{
    public function __construct(
        public readonly string $gameShort,
        public readonly int $planId,
        public readonly string $region = 'eu',
        public readonly ?int $templateId = null,
        public readonly ?int $templateVersionId = null,
        public readonly int $instanceCount = 1,
    ) {
        $this->assertValid();
    }

    public function normalizedGameShort(): string
    {
        return strtolower(trim($this->gameShort));
    }

    /**
     * @return array<string, int|string>
     */
    public function toPayload(): array
    {
        $payload = [
            'game_short' => $this->normalizedGameShort(),
            'plan_id' => $this->planId,
            'region' => $this->region,
            'instance_count' => $this->instanceCount,
        ];

        if ($this->templateId !== null) {
            $payload['template_id'] = $this->templateId;
        }

        if ($this->templateVersionId !== null) {
            $payload['template_version_id'] = $this->templateVersionId;
        }

        return $payload;
    }

    protected function assertValid(): void
    {
        if ($this->normalizedGameShort() === '') {
            throw new InvalidRentRequestException('The rent parameter "game_short" is required.');
        }

        if ($this->planId < 0) {
            throw new InvalidRentRequestException('The rent parameter "plan_id" must be greater than or equal to 0.');
        }

        if (! in_array($this->region, ['eu', 'us'], true)) {
            throw new InvalidRentRequestException('The rent parameter "region" must be one of: eu, us.');
        }

        if ($this->templateId !== null && $this->templateId < 1) {
            throw new InvalidRentRequestException('The rent parameter "template_id" must be greater than or equal to 1.');
        }

        if ($this->templateVersionId !== null && $this->templateVersionId < 1) {
            throw new InvalidRentRequestException('The rent parameter "template_version_id" must be greater than or equal to 1.');
        }

        if ($this->instanceCount < 1) {
            throw new InvalidRentRequestException('The rent parameter "instance_count" must be greater than or equal to 1.');
        }
    }
}
