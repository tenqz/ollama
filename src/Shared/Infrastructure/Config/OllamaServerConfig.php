<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Shared\Infrastructure\Config;

/**
 * Configuration for Ollama server connection.
 */
class OllamaServerConfig
{
    /**
     * @var string Host address (domain or IP)
     */
    private $host;

    /**
     * @var int Port number
     */
    private $port;

    /**
     * @param string $host Host address (domain or IP)
     * @param int $port Port number
     */
    public function __construct(string $host = 'localhost', int $port = 11434)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Get full base URL for API requests.
     *
     * @return string Base URL (e.g., "http://localhost:11434")
     */
    public function getBaseUrl(): string
    {
        return sprintf('http://%s:%d', $this->host, $this->port);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
}
