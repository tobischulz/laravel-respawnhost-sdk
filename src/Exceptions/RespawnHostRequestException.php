<?php

namespace TobiSchulz\LaravelRespawnHostSdk\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class RespawnHostRequestException extends RuntimeException
{
    public function __construct(string $message, protected Response $response)
    {
        parent::__construct($message, $response->status());
    }

    public static function fromResponse(Response $response): self
    {
        $message = "RespawnHost API request failed with status {$response->status()}.";
        $body = $response->json();

        if (is_array($body)) {
            foreach (['message', 'error', 'detail'] as $key) {
                $candidate = $body[$key] ?? null;

                if (is_string($candidate) && $candidate !== '') {
                    $message = $candidate;
                    break;
                }
            }
        }

        return new self($message, $response);
    }

    public function response(): Response
    {
        return $this->response;
    }
}
