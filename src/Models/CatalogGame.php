<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Models;

class CatalogGame
{
    /**
     * @param  list<CatalogGamePackage>  $packages
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $short,
        public readonly ?int $eggId,
        public readonly ?bool $topGame,
        public readonly ?int $neededPorts,
        public readonly ?string $portMapping,
        public readonly mixed $connectionConfig,
        public readonly ?string $portRange,
        public readonly ?string $defaultImage,
        public readonly ?bool $isReleased,
        public readonly ?bool $isActive,
        public readonly ?bool $isWindows,
        public readonly ?bool $supportsSteam,
        public readonly ?bool $supportsPs,
        public readonly ?bool $supportsXbox,
        public readonly ?bool $supportsCrossplay,
        public readonly ?bool $supportsMobile,
        public readonly ?bool $supportsPc,
        public readonly ?bool $hasAutoStop,
        public readonly ?bool $supportsMultiInstance,
        public readonly mixed $defaultPorts,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly array $packages = [],
        public readonly array $raw = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $packagesRaw = $data['game_package'] ?? $data['gamePackage'] ?? [];
        $packages = [];

        if (is_array($packagesRaw)) {
            foreach ($packagesRaw as $package) {
                if (is_array($package)) {
                    $packages[] = CatalogGamePackage::fromArray($package);
                }
            }
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            short: (string) ($data['short'] ?? ''),
            eggId: self::toNullableInt($data['egg_id'] ?? $data['eggId'] ?? null),
            topGame: self::toNullableBool($data['top_game'] ?? $data['topGame'] ?? null),
            neededPorts: self::toNullableInt($data['needed_ports'] ?? $data['neededPorts'] ?? null),
            portMapping: self::toNullableString($data['port_mapping'] ?? $data['portMapping'] ?? null),
            connectionConfig: $data['connectionConfig'] ?? null,
            portRange: self::toNullableString($data['portRange'] ?? null),
            defaultImage: self::toNullableString($data['default_image'] ?? $data['defaultImage'] ?? null),
            isReleased: self::toNullableBool($data['is_released'] ?? $data['isReleased'] ?? null),
            isActive: self::toNullableBool($data['is_active'] ?? $data['isActive'] ?? null),
            isWindows: self::toNullableBool($data['isWindows'] ?? null),
            supportsSteam: self::toNullableBool($data['supportsSteam'] ?? null),
            supportsPs: self::toNullableBool($data['supportsPs'] ?? null),
            supportsXbox: self::toNullableBool($data['supportsXbox'] ?? null),
            supportsCrossplay: self::toNullableBool($data['supportsCrossplay'] ?? null),
            supportsMobile: self::toNullableBool($data['supportsMobile'] ?? null),
            supportsPc: self::toNullableBool($data['supportsPc'] ?? null),
            hasAutoStop: self::toNullableBool($data['hasAutoStop'] ?? null),
            supportsMultiInstance: self::toNullableBool($data['supportsMultiInstance'] ?? null),
            defaultPorts: $data['defaultPorts'] ?? null,
            createdAt: self::toNullableString($data['created_at'] ?? $data['createdAt'] ?? null),
            updatedAt: self::toNullableString($data['updated_at'] ?? $data['updatedAt'] ?? null),
            packages: $packages,
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
            'short' => $this->short,
            'egg_id' => $this->eggId,
            'top_game' => $this->topGame,
            'needed_ports' => $this->neededPorts,
            'port_mapping' => $this->portMapping,
            'connection_config' => $this->connectionConfig,
            'port_range' => $this->portRange,
            'default_image' => $this->defaultImage,
            'is_released' => $this->isReleased,
            'is_active' => $this->isActive,
            'is_windows' => $this->isWindows,
            'supports_steam' => $this->supportsSteam,
            'supports_ps' => $this->supportsPs,
            'supports_xbox' => $this->supportsXbox,
            'supports_crossplay' => $this->supportsCrossplay,
            'supports_mobile' => $this->supportsMobile,
            'supports_pc' => $this->supportsPc,
            'has_auto_stop' => $this->hasAutoStop,
            'supports_multi_instance' => $this->supportsMultiInstance,
            'default_ports' => $this->defaultPorts,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'game_package' => array_map(
                static fn (CatalogGamePackage $package): array => $package->toArray(),
                $this->packages,
            ),
        ];
    }

    protected static function toNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected static function toNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = (string) $value;

        return $string === '' ? null : $string;
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
