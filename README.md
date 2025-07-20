<p align="center">
<img src="logo.png" alt="Ollama PHP Client Library" width="200">
</p>

<h1 align="center">Ollama PHP Client Library</h1>

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

## Basic Usage

```php
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

// Initialize the client
$client = new CurlTransportClient('http://localhost:11434');

// Generate text using a model
$response = $client->post('/api/generate', [
    'model' => 'llama2',
    'prompt' => 'What is artificial intelligence?'
]);

// Get the generated text
$result = $response->getData();
echo $result['response'];
```

## Requirements

- PHP 7.2 or higher
- cURL extension

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
