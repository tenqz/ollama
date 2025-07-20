<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Shared\Infrastructure\Http\Response;

/**
 * HTTP response wrapper.
 */
class HttpResponse
{
    /** @var int */
    private $statusCode;
    /** @var array<string, mixed> */
    private $headers;
    /** @var string */
    private $body;

    /**
     * @param int                  $statusCode HTTP status code
     * @param array<string, mixed> $headers    Response headers
     * @param string               $body       Response body
     */
    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get response headers.
     *
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Check if response is successful (2xx status code).
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
