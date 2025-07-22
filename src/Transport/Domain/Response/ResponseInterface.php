<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Transport\Domain\Response;

/**
 * Interface for API response.
 *
 * Defines contract for handling API responses.
 */
interface ResponseInterface
{
    /**
     * Get HTTP status code.
     *
     * @return int HTTP status code
     */
    public function getStatusCode(): int;

    /**
     * Check if response is successful (2xx status code).
     *
     * @return bool True if response is successful, false otherwise
     */
    public function isSuccessful(): bool;

    /**
     * Get response headers.
     *
     * @return array<string, mixed> Response headers
     */
    public function getHeaders(): array;

    /**
     * Get raw response body.
     *
     * @return string Raw response body
     */
    public function getBody(): string;

    /**
     * Get response data as array.
     *
     * @return array<string, mixed> Response data
     *
     * @throws \Tenqz\Ollama\Transport\Domain\Exception\TransportException When response cannot be decoded
     */
    public function getData(): array;
}
