<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Transport\Domain\Exception;

/**
 * Exception thrown when transport error occurs.
 */
class TransportException extends \Exception
{
    /** @var int|null */
    private $statusCode;

    /** @var array<string, mixed>|null */
    private $responseData;

    /**
     * Create exception with optional status code and response data.
     *
     * @param string                 $message      Exception message
     * @param int                    $code         Exception code
     * @param \Throwable|null        $previous     Previous exception
     * @param int|null               $statusCode   HTTP status code
     * @param array<string, mixed>|null $responseData Response data
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        ?int $statusCode = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->responseData = $responseData;
    }

    /**
     * Get HTTP status code.
     *
     * @return int|null HTTP status code or null if not available
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Get response data.
     *
     * @return array<string, mixed>|null Response data or null if not available
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
