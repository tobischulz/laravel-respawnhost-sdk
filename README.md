# Laravel RespawnHost SDK

[![Tests](https://github.com/tobischulz/laravel-respawnhost-sdk/actions/workflows/tests.yml/badge.svg)](https://github.com/tobischulz/laravel-respawnhost-sdk/actions/workflows/tests.yml)

Laravel package to integrate with the official [RespawnHost API](https://developers.respawnhost.com/api-reference) and manage game servers from your application.

## Current scope

This repository now contains a stable SDK foundation:

- centralized HTTP client with Bearer API key authentication
- configurable base URL, timeouts, retries, and user agent
- first resource clients for:
  - `servers`
  - `payments`
  - `transactions`
  - `catalog` (public games + packages)
- dedicated exception type for failed API responses
- test baseline with Pest + Testbench

The current implementation was prepared against the public RespawnHost OpenAPI document (`version: 1.0.0`) and product context from [respawnhost.com](https://respawnhost.com) on February 13, 2026.

## Installation

```bash
composer require tobischulz/laravel-respawnhost-sdk
```

Publish the configuration:

```bash
php artisan vendor:publish --tag="laravel-respawnhost-sdk-config"
```

## Configuration

Set these environment variables:

```dotenv
RESPAWNHOST_BASE_URL=https://respawnhost.com/api/v1
RESPAWNHOST_API_KEY=your-api-key
RESPAWNHOST_TIMEOUT=30
RESPAWNHOST_CONNECT_TIMEOUT=10
RESPAWNHOST_RETRY_TIMES=1
RESPAWNHOST_RETRY_SLEEP=200
RESPAWNHOST_USER_AGENT=laravel-respawnhost-sdk
RESPAWNHOST_CATALOG_BASE_URL=https://respawnhost.com
```

Default config file (`config/respawnhost-sdk.php`):

```php
return [
    'base_url' => env('RESPAWNHOST_BASE_URL', 'https://respawnhost.com/api/v1'),
    'api_key' => env('RESPAWNHOST_API_KEY'),
    'timeout' => (int) env('RESPAWNHOST_TIMEOUT', 30),
    'connect_timeout' => (int) env('RESPAWNHOST_CONNECT_TIMEOUT', 10),
    'retry' => [
        'times' => (int) env('RESPAWNHOST_RETRY_TIMES', 1),
        'sleep' => (int) env('RESPAWNHOST_RETRY_SLEEP', 200),
    ],
    'user_agent' => env('RESPAWNHOST_USER_AGENT', 'laravel-respawnhost-sdk'),
    'catalog_base_url' => env('RESPAWNHOST_CATALOG_BASE_URL', 'https://respawnhost.com'),
];
```

## API Token (Dev and Production)

As of February 13, 2026, the public RespawnHost docs state that API keys are created in the account dashboard:

- [Authentication](https://developers.respawnhost.com/docs/authentication)
- [FAQ](https://developers.respawnhost.com/docs/faq)

Directlink to api-key-management Dashboard:

- [https://respawnhost.com/de/api-keys](https://respawnhost.com/de/api-keys)

This route currently redirects unauthenticated users to login and is not explicitly documented in the public developer docs, so it may change.

### Token for dev environment

1. Log in to your RespawnHost dashboard.
2. Open API key management from dashboard settings (or use the direct URL above).
3. Create a dedicated dev key with only required scopes.
4. Use it with the dev API base URL:

```dotenv
RESPAWNHOST_BASE_URL=https://respawnhost.com/api/v1
RESPAWNHOST_API_KEY=your-dev-api-key
```

### Token for production environment

1. Create a separate production key (do not reuse the dev key).
2. Assign only required production scopes.
3. Use it with the production API base URL:

```dotenv
RESPAWNHOST_BASE_URL=https://respawnhost.com/api/v1
RESPAWNHOST_API_KEY=your-production-api-key
```

Recommended: keep separate keys per app/environment and rotate them regularly.

## Usage

Use the facade:

```php
use TobiSchulz\LaravelRespawnHostSdk\Facades\RespawnHost;

$servers = RespawnHost::servers()->all(['page' => 1, 'limit' => 10]);
$server = RespawnHost::servers()->find('server-uuid');
$serverWithPanelData = RespawnHost::servers()->find('server-uuid', includePanelServer: true);

RespawnHost::servers()->sendCommand('server-uuid', [
    'command' => 'say Hello from Laravel SDK',
]);
```

### Rent a server (typed wrapper)

You can call server rent directly via facade with typed parameters:

```php
use TobiSchulz\LaravelRespawnHostSdk\Facades\RespawnHost;

$result = RespawnHost::rent(
    gameShort: 'enshrouded', // required
    planId: 324,             // required
    region: 'eu',            // optional: eu|us
    templateId: null,        // optional
    templateVersionId: null, // optional
    instanceCount: 1,        // optional, min 1
);
```

Validation behavior:

- required parameters (`gameShort`, `planId`) must be present
- invalid values (for example wrong `region` or `instanceCount < 1`) throw an exception before the HTTP request
- for pre-validation of `gameShort`, use `RespawnHost::gameByShort($gameShort)` before rent

### Public game catalog (typed models)

The package now supports these public catalog endpoints:

- `GET /api/games`
- `GET /api/games/short/{game_short}`
- `GET /api/games/short/{game_short}/packages`

These endpoints are fetched through the SDK without requiring `RESPAWNHOST_API_KEY`.

Usage:

```php
use TobiSchulz\LaravelRespawnHostSdk\Facades\RespawnHost;

// list<CatalogGame>
$games = RespawnHost::allGames();

// CatalogGame
$game = RespawnHost::gameByShort('v-rising');

// list<CatalogGamePackage>
$packages = RespawnHost::packagesByGameShort('v-rising');
```

These methods return immutable DTO-style classes:

- `TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGame`
- `TobiSchulz\LaravelRespawnHostSdk\Models\CatalogGamePackage`

Payments and transactions:

```php
$payments = RespawnHost::payments()->all(['page' => 1]);
$invoice = RespawnHost::payments()->downloadInvoice(123);

$transactions = RespawnHost::transactions()->all();
$transaction = RespawnHost::transactions()->find(456);
```

For endpoints that are not wrapped yet, use the generic request method:

```php
$response = RespawnHost::request(
    method: 'GET',
    uri: '/api/v1/servers/server-uuid/files',
    query: ['directory' => '/']
);
```

Error handling:

```php
use TobiSchulz\LaravelRespawnHostSdk\Exceptions\RespawnHostRequestException;

try {
    RespawnHost::transactions()->all();
} catch (RespawnHostRequestException $exception) {
    $status = $exception->response()->status();
    $body = $exception->response()->json();
}
```

## Development

```bash
composer install
composer test
composer analyse
composer format
```

## Roadmap

- expand endpoint coverage based on OpenAPI tags:
  - `servers/*` (files, backups, schedules, shares, minecraft, databases)
  - `payments`
  - `transactions`
- add request/response DTOs and stronger typing
- add contract tests for critical workflows (rent, powerstate, backups, billing)

Detailed endpoint mapping is tracked in [`docs/API-COVERAGE.md`](docs/API-COVERAGE.md).

## License

MIT. See [LICENSE.md](LICENSE.md).
