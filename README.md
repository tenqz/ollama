<p align="center">
<img src="logo.png" alt="Ollama PHP Client Library" width="200">
</p>

<h1 align="center">Ollama PHP Client Library</h1>

<p align="center">
<span style="font-size: 1.2em;">Documentation for version v0.4.0</span>
</p>

<p align="center">
<a href="https://github.com/tenqz/ollama/actions"><img src="https://github.com/tenqz/ollama/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/tenqz/ollama"><img src="https://img.shields.io/packagist/dt/tenqz/ollama" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tenqz/ollama"><img src="https://img.shields.io/packagist/v/tenqz/ollama" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/tenqz/ollama"><img src="https://img.shields.io/packagist/l/tenqz/ollama" alt="License"></a>
</p>

## About

Ollama PHP Client Library is a robust, well-designed PHP client for interacting with the Ollama API. This library allows PHP developers to easily integrate large language models (LLMs) into their applications using the Ollama server.

## Features

- Clean, domain-driven architecture
- Simple HTTP transport layer
- Full support for Ollama API endpoints
- Type-safe request and response handling
- PSR standards compliance
- Comprehensive test coverage

## Installation

You can install the package via composer:

```bash
composer require tenqz/ollama
```

### Examples

Runnable examples are available in the `examples/` directory for quick local testing with Ollama:

- `examples/generate.php` — basic text generation with configurable host/port/timeout and optional metadata output. See `examples/README.md` for usage details and environment variables.

Notes:
- Ensure the Ollama server is running (default `http://localhost:11434`) and the selected model is available on the server.

## Usage

### Using API Client Pattern

```php
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Infrastructure\Client\OllamaGenerationClient;
use Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig;
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

// Configure the server connection
$config = new OllamaServerConfig('localhost', 11434);

// Initialize the transport client
$transportClient = new CurlTransportClient($config->getBaseUrl());

// Create the API client
$apiClient = new OllamaGenerationClient($transportClient);

// Create a generation request
$request = new GenerationRequest('llama3.2');
$request->setPrompt('What is artificial intelligence?');

// Generate text using the API client
$response = $apiClient->generate($request);

// Get the generated text
echo $response->getResponse();

// Access metadata if available
echo 'Model: ' . $response->getModel();
echo 'Created At: ' . $response->getCreatedAt();
```

## Architecture

The library follows Domain-Driven Design principles with a clear separation of concerns:

### Transport Layer
- `Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface` - Interface for HTTP clients
- `Tenqz\Ollama\Transport\Domain\Response\ResponseInterface` - Interface for API responses
- `Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient` - cURL implementation

### Generation Layer
- `Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest` - Request DTO for text generation
- `Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse` - Response DTO for generated text
- `Tenqz\Ollama\Generation\Domain\Client\GenerationClientInterface` - Client interface for generation operations
- `Tenqz\Ollama\Generation\Infrastructure\Client\OllamaGenerationClient` - Implementation of generation client

### Shared Layer
- `Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig` - Configuration for Ollama server connection
- `Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints` - API endpoints constants
- Contains cross-cutting concerns and components that are used by multiple domains

## Requirements

- PHP 7.2 or higher
- cURL extension

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
