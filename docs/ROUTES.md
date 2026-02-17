# RespawnHost Laravel SDK API Routes

This document outlines the API endpoints utilized by the RespawnHost Laravel SDK.

## Catalog Endpoints (Base URL: `https://respawnhost.com`)

These endpoints are used for public catalog information and do not require an API key.

- `GET /api/games` - Retrieve a list of all public catalog games.
- `GET /api/games/short/{gameShort}` - Retrieve details for a specific game using its short name.
- `GET /api/games/short/{gameShort}/packages` - Retrieve packages available for a specific game using its short name.

## Authenticated Endpoints (Base URL: `https://respawnhost.com/api/v1`)

These endpoints require an API key for authentication. The `/api/v1` prefix is now part of the base URL.

### Payments

- `GET /payments` - Retrieve a list of all payments.
- `GET /payments/{paymentId}/download` - Download an invoice for a specific payment.

### Servers

- `GET /servers` - Retrieve a list of all servers.
- `GET /servers/{uuid}` - Retrieve details for a specific server using its UUID.
- `POST /servers/rent` - Rent a new server.
- `DELETE /servers/{uuid}` - Delete a server.
- `POST /servers/{uuid}/send-command` - Send a command to a specific server.
- `POST /servers/{uuid}/powerstate` - Change the power state of a specific server (e.g., start, stop, restart).
- `POST /servers/{uuid}/reinstall` - Reinstall a specific server.
- `GET /servers/{uuid}/resource-utilization` - Retrieve resource utilization statistics for a specific server.
- `GET /servers/{uuid}/transactions` - Retrieve transactions related to a specific server.
- `[DYNAMIC_METHOD] /servers/{uuid}/{suffix}` - A generic endpoint for raw server interactions. The HTTP method and the `suffix` are dynamically provided.

### Transactions

- `GET /transactions` - Retrieve a list of all transactions.
- `GET /transactions/{transactionId}` - Retrieve details for a specific transaction.
