<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Shared\Infrastructure\Api;

/**
 * Ollama API endpoints.
 */
final class OllamaApiEndpoints
{
    /**
     * Generate text endpoint.
     */
    public const GENERATE = '/api/generate';

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }
}
