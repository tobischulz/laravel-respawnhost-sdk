# Changelog

All notable changes to `tobischulz/laravel-respawnhost-sdk` will be documented in this file.

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
