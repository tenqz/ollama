<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Transport\Infrastructure\Http\Client;

use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Interfaces\ResponseInterface;
use Tenqz\Ollama\Transport\Domain\Interfaces\TransportClientInterface;
use Tenqz\Ollama\Transport\Infrastructure\Http\Response\JsonResponse;

/**
 * cURL implementation of TransportClientInterface.
 */
class CurlTransportClient implements TransportClientInterface
{
    /** @var string */
    private $baseUrl;

    /** @var array<string, string> */
    private $defaultHeaders;

    /** @var int */
    private $timeout;

    /**
     * @param string               $baseUrl        Base URL for API
     * @param array<string, string> $defaultHeaders Default headers for all requests
     * @param int                  $timeout        Request timeout in seconds
     *
     * @throws TransportException When curl extension is not loaded
     */
    public function __construct(
        string $baseUrl,
        array $defaultHeaders = [],
        int $timeout = 30
    ) {
        if (!extension_loaded('curl')) {
            throw new TransportException('cURL extension is not loaded. Please install or enable the PHP curl extension.');
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->defaultHeaders = array_merge(
            ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            $defaultHeaders
        );
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $endpoint, array $params = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);
        $result = $this->executeCurlRequest($url, [], 'GET', $params);

        return new JsonResponse($result['status'], $result['headers'], $result['body']);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $endpoint, array $data = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);
        $result = $this->executeCurlRequest($url, $data, 'POST');

        return new JsonResponse($result['status'], $result['headers'], $result['body']);
    }

    /**
     * Build full URL from endpoint.
     *
     * @param string $endpoint API endpoint
     *
     * @return string Full URL
     */
    protected function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');

        return $this->baseUrl . '/' . $endpoint;
    }

    /**
     * Build URL with query parameters.
     *
     * @param string                                    $url    Base URL
     * @param array<string, string|int|float|bool|null> $params Query parameters
     *
     * @return string URL with query parameters
     */
    protected function buildUrlWithParams(string $url, array $params = []): string
    {
        if (empty($params)) {
            return $url;
        }

        return $url . '?' . http_build_query($params);
    }

    /**
     * Initialize cURL resource.
     *
     * @param string $url URL to request
     *
     * @return resource cURL resource
     *
     * @throws TransportException When cURL initialization fails
     */
    protected function initCurl(string $url)
    {
        $curl = curl_init();
        if ($curl === false) {
            throw new TransportException('Failed to initialize cURL');
        }

        // Set basic cURL options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_HEADER, true);

        return $curl;
    }

    /**
     * Set HTTP headers for cURL request.
     *
     * @param resource             $curl    cURL resource
     * @param array<string, string> $headers Headers to set
     */
    protected function setCurlHeaders($curl, array $headers): void
    {
        $formattedHeaders = [];
        foreach ($headers as $name => $value) {
            $formattedHeaders[] = $name . ': ' . $value;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $formattedHeaders);
    }

    /**
     * Set HTTP method and request data for cURL.
     *
     * @param resource              $curl   cURL resource
     * @param string                $method HTTP method
     * @param array<string, mixed>  $data   Request data
     */
    protected function setCurlMethod($curl, string $method, array $data = []): void
    {
        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }

                break;
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);

                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
        }
    }

    /**
     * Execute cURL request and handle errors.
     *
     * @param resource $curl cURL resource
     *
     * @return string Raw response
     *
     * @throws TransportException When cURL execution fails
     */
    protected function executeCurl($curl): string
    {
        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            $errno = curl_errno($curl);
            curl_close($curl);

            throw new TransportException('cURL error: ' . $error, $errno);
        }

        /** @var string $response */
        return $response;
    }

    /**
     * Extract response info from cURL response.
     *
     * @param resource $curl     cURL resource
     * @param string   $response Raw response
     *
     * @return array{status: int, headers: array<string, string>, body: string} Response data
     */
    protected function extractResponseInfo($curl, string $response): array
    {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $headerText = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $headers = $this->parseHeaders($headerText);

        return [
            'status'  => $statusCode,
            'headers' => $headers,
            'body'    => $body,
        ];
    }

    /**
     * Execute cURL request.
     *
     * @param string                                           $url    URL to request
     * @param array<string, mixed>                             $data   Request data for POST requests
     * @param string                                           $method HTTP method (GET, POST, etc.)
     * @param array<string, string|int|float|bool|null>        $params Query parameters for GET requests
     *
     * @return array{status: int, headers: array<string, string>, body: string} Response data
     *
     * @throws TransportException When transport error occurs
     */
    protected function executeCurlRequest(
        string $url,
        array $data = [],
        string $method = 'GET',
        array $params = []
    ): array {
        // Build URL with query parameters
        $url = $this->buildUrlWithParams($url, $params);

        // Initialize cURL
        $curl = $this->initCurl($url);

        // Set headers
        $this->setCurlHeaders($curl, $this->defaultHeaders);

        // Set method and data
        $this->setCurlMethod($curl, $method, $data);

        // Execute request
        $response = $this->executeCurl($curl);

        // Extract response info
        $result = $this->extractResponseInfo($curl, $response);

        curl_close($curl);

        return $result;
    }

    /**
     * Parse HTTP headers from string.
     *
     * @param string $headerText Raw headers string
     *
     * @return array<string, string> Parsed headers
     */
    private function parseHeaders(string $headerText): array
    {
        $headers = [];

        // Split headers by line
        $headerLines = preg_split('/[\r\n]+/', $headerText);
        if ($headerLines === false) {
            return [];
        }

        // Process each line
        foreach ($headerLines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Skip HTTP status line
            if (strpos($line, 'HTTP/') === 0) {
                continue;
            }

            // Split by first colon
            $parts = explode(':', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            $headers[$key] = $value;
        }

        return $headers;
    }
}
