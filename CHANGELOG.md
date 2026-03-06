# Changelog

All notable changes to `tobischulz/laravel-respawnhost-sdk` will be documented in this file.

## v1.1.0 - 2026-03-06

- Added optional `includePanelServer` query support to `servers()->find($uuid, ?bool $includePanelServer = null)`
- Removed static `games` config whitelist for rent validation in favor of live catalog lookups
- Improved public catalog integration (`/api/games`, `/api/games/short/{game_short}`, `/api/games/short/{game_short}/packages`) with typed DTO mapping
- Updated README with dev/prod API token guidance and `includePanelServer` usage examples
- Extended test coverage for server lookup query behavior (`includePanelServer` default/true/false)

## v1.0.0 - 2026-02-25

- Initial release
- Configurable RespawnHost API client with Bearer token authentication
- Laravel service provider and `RespawnHost` facade
- Resource clients for servers, payments, transactions, and public catalog
- Public catalog endpoints (games, packages) without API key required
- Typed `rent()` wrapper with parameter validation
- `CatalogGame` and `CatalogGamePackage` DTO models
- `RespawnHostRequestException` for failed API responses
- Pest + Testbench test baseline
