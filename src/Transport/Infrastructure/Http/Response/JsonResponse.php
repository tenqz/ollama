<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Transport\Infrastructure\Http\Response;

use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Interfaces\ResponseInterface;

/**
 * JSON API response implementation.
 */
class JsonResponse implements ResponseInterface
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
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        $data = json_decode($this->body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TransportException(
                'Failed to decode JSON response: ' . json_last_error_msg(),
                json_last_error(),
                null,
                $this->statusCode
            );
        }

        if (!is_array($data)) {
            return ['data' => $data];
        }

        return $data;
    }
}
