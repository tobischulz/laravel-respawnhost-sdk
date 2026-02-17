<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Models;

class CatalogGamePackage
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $gameId,
        public readonly ?int $memory,
        public readonly ?int $cpu,
        public readonly ?int $disk,
        public readonly ?string $priceHourly,
        public readonly ?string $priceMonthly,
        public readonly ?int $recommendedPlayers,
        public readonly ?bool $isPopular,
        public readonly ?int $serverCount,
        public readonly ?string $slug,
        public readonly ?string $price,
        public readonly array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            gameId: isset($data['game_id']) ? (int) $data['game_id'] : null,
            memory: isset($data['memory']) ? (int) $data['memory'] : null,
            cpu: isset($data['cpu']) ? (int) $data['cpu'] : null,
            disk: isset($data['disk']) ? (int) $data['disk'] : null,
            priceHourly: isset($data['price_hourly']) ? (string) $data['price_hourly'] : null,
            priceMonthly: isset($data['price_monthly']) ? (string) $data['price_monthly'] : null,
            recommendedPlayers: isset($data['recommended_players']) ? (int) $data['recommended_players'] : null,
            isPopular: self::toNullableBool($data['is_popular'] ?? null),
            serverCount: isset($data['server_count']) ? (int) $data['server_count'] : null,
            slug: isset($data['slug']) ? (string) $data['slug'] : null,
            price: isset($data['price']) ? (string) $data['price'] : null,
            raw: $data,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'game_id' => $this->gameId,
            'memory' => $this->memory,
            'cpu' => $this->cpu,
            'disk' => $this->disk,
            'price_hourly' => $this->priceHourly,
            'price_monthly' => $this->priceMonthly,
            'recommended_players' => $this->recommendedPlayers,
            'is_popular' => $this->isPopular,
            'server_count' => $this->serverCount,
            'slug' => $this->slug,
            'price' => $this->price,
        ];
    }

    protected static function toNullableBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            if (in_array($normalized, ['true', '1', 'yes'], true)) {
                return true;
            }

            if (in_array($normalized, ['false', '0', 'no'], true)) {
                return false;
            }
        }

        return null;
    }
}
