<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Transport\Domain\Interface;

/**
 * Interface for transport client.
 *
 * Defines contract for sending HTTP requests to API.
 */
interface TransportClientInterface
{
         /**
     * Send GET request.
     *
     * @param string $endpoint API endpoint
     * @param array<string, string|int|float|bool|null>  $params   Query parameters
     *
     * @return ResponseInterface Response object
     *
     * @throws \Tenqz\Ollama\Transport\Domain\Exception\TransportException When transport error occurs
     */
    public function get(string $endpoint, array $params = []): ResponseInterface;

         /**
     * Send POST request.
     *
     * @param string $endpoint API endpoint
     * @param array<string, mixed>  $data     Request data
     *
     * @return ResponseInterface Response object
     *
     * @throws \Tenqz\Ollama\Transport\Domain\Exception\TransportException When transport error occurs
     */
    public function post(string $endpoint, array $data = []): ResponseInterface;
}
