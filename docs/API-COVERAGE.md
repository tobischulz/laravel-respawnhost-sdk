# RespawnHost API Coverage

This package setup is based on the public RespawnHost OpenAPI spec (`version: 1.0.0`) from:

- https://developers.respawnhost.com/api-reference
- https://developers.respawnhost.com/openapi.json

Retrieved on February 13, 2026.

Additional public catalog endpoints validated on February 13, 2026:

- `https://respawnhost.com/api/games`
- `https://respawnhost.com/api/games/short/{game_short}`
- `https://respawnhost.com/api/games/short/{game_short}/packages`

## OpenAPI domains

- `servers`
- `servers/backups`
- `servers/databases`
- `servers/files`
- `servers/minecraft`
- `servers/schedules`
- `servers/shares`
- `payments`
- `transactions`

## Wrapped endpoints (initial)

### Servers

- `GET /api/v1/servers`
- `GET /api/v1/servers/{uuid}`
- `POST /api/v1/servers/rent`
- `DELETE /api/v1/servers/{uuid}`
- `POST /api/v1/servers/{uuid}/send-command`
- `POST /api/v1/servers/{uuid}/powerstate`
- `POST /api/v1/servers/{uuid}/reinstall`
- `GET /api/v1/servers/{uuid}/resource-utilization`
- `GET /api/v1/servers/{uuid}/transactions`

### Payments

- `GET /api/v1/payments`
- `GET /api/v1/payments/{id}/download`

### Transactions

- `GET /api/v1/transactions`
- `GET /api/v1/transactions/{id}`

### Public catalog

- `GET /api/games`
- `GET /api/games/short/{game_short}`
- `GET /api/games/short/{game_short}/packages`

## Auth model

- Security scheme: `BearerAuth` (HTTP Bearer token / API key)
- Typical failures:
  - `401` when no API key is provided
  - `403` when API key scope is missing (examples in OpenAPI include `server.read`, `server.write`, `server.files`, `payment.read`, `transaction.read`)

## Intentionally not wrapped

- `GET /api/v1/servers/{uuid}/websocket`
  - Reason: the SDK is focused on REST workflows from PHP. Real-time WebSocket consumption is better handled by frontend clients or long-running worker processes, not the default request/response lifecycle.
