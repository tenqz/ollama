<p align="center">
<img src="logo.png" alt="Ollama PHP Client Library" width="200">
</p>

<h1 align="center">Ollama PHP Client Library</h1>

<p align="center">
<span style="font-size: 1.2em;">Documentation for version v0.6.0</span>
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

- **Clean, domain-driven architecture** with clear separation of concerns
- **Comprehensive Ollama API support** including text generation and embeddings
- **Type-safe request and response handling** with full DTO support
- **Text generation** with advanced options (temperature, top-k, top-p, repetition penalty, and more)
- **Text embeddings** for semantic search, similarity, and vector operations
- **Multimodal support** for image inputs with base64-encoded images
- **Streaming support** for real-time text generation
- **Flexible configuration** with customizable timeouts and connection settings
- **PSR standards compliance** with proper interfaces and abstractions
- **Comprehensive test coverage** with 97+ unit tests for embeddings alone

## Installation

You can install the package via composer:

```bash
composer require tenqz/ollama
```

## Requirements

- **PHP 7.2 or higher** (supports PHP 8.0+ features)
- **cURL extension** for HTTP communication
- **JSON extension** for data serialization
- **Ollama server** running locally or remotely

## Usage

### Text Generation

```php
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationOptions;
use Tenqz\Ollama\Generation\Infrastructure\Client\OllamaGenerationClient;
use Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig;
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

// Configure the server connection
$config = new OllamaServerConfig('localhost', 11434);
$transportClient = new CurlTransportClient($config->getBaseUrl());

// Create the Generation API client
$client = new OllamaGenerationClient($transportClient);

// Create a generation request with options
$request = new GenerationRequest('llama3.2');
$request->setPrompt('Write a creative story about AI');
$request->setSystem('You are a creative writing assistant.');

// Configure generation options
$options = new GenerationOptions();
$options->setTemperature(0.8);      // More creative
$options->setTopK(40);              // Vocabulary diversity
$options->setNumPredict(500);       // Max tokens
$request->setOptions($options);

// Generate text
$response = $client->generate($request);
echo $response->getResponse();
```

### Text Embeddings

```php
use Tenqz\Ollama\Embedding\Application\DTO\Request\EmbeddingRequest;
use Tenqz\Ollama\Embedding\Infrastructure\Client\OllamaEmbeddingClient;
use Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig;
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

// Configure the server connection
$config = new OllamaServerConfig('localhost', 11434);
$transportClient = new CurlTransportClient($config->getBaseUrl());

// Create the Embedding API client
$client = new OllamaEmbeddingClient($transportClient);

// Create an embedding request
$request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

// Generate embedding vector
$response = $client->embed($request);

// Access the embedding vector
$embedding = $response->getEmbedding();        // First embedding (768-dimensional vector)
$dimension = $response->getDimension();        // Vector dimension (e.g., 768)

// Use embeddings for similarity search, clustering, etc.
echo "Embedding dimension: {$dimension}\n";
echo "First 5 values: " . implode(', ', array_slice($embedding, 0, 5));
```

## Architecture

The library follows Domain-Driven Design principles with a clear separation of concerns across multiple layers:

### Transport Layer
- **`TransportClientInterface`** - Interface for HTTP clients with GET/POST methods
- **`ResponseInterface`** - Interface for API responses with status and data access
- **`CurlTransportClient`** - cURL implementation with configurable timeouts and headers
- **`JsonResponse`** - JSON response implementation with data parsing

### Generation Layer (Text Generation)
- **`GenerationRequest`** - Request DTO with prompts, options, images, streaming, templates
- **`GenerationOptions`** - Fine-grained control (temperature, top-k, top-p, repetition penalty, etc.)
- **`GenerationResponse`** - Response DTO with generated text and metadata
- **`GenerationClientInterface`** - Client interface for generation operations
- **`OllamaGenerationClient`** - Implementation with error handling and response transformation
- **`GenerationException`** - Domain-specific exception for generation errors

### Embedding Layer (Text Embeddings)
- **`EmbeddingRequest`** - Request DTO with model and input text
- **`EmbeddingResponse`** - Response DTO with embedding vectors (supports batch processing)
- **`EmbeddingClientInterface`** - Client interface for embedding operations
- **`OllamaEmbeddingClient`** - Implementation with error handling and vector processing
- **`EmbeddingException`** - Domain-specific exception for embedding errors

### Shared Layer
- **`OllamaServerConfig`** - Server configuration with host, port, and URL building
- **`OllamaApiEndpoints`** - API endpoint constants (`/api/generate`, `/api/embed`)
- Cross-cutting concerns and utilities used across domains

## Advanced Features

### Generation Options
The library supports comprehensive generation options for fine-tuning model behavior:

**Sampling Parameters:**
- `temperature` (0.0-1.0) - Controls randomness (higher = more creative)
- `top_k` (1-100) - Limits vocabulary diversity 
- `top_p` (0.0-1.0) - Nucleus sampling for focused responses
- `seed` (integer) - Deterministic outputs for reproducible results

**Generation Control:**
- `num_predict` (integer) - Maximum tokens to generate
- `repeat_penalty` (float) - Penalty for repetition
- `stop` (array) - Stop sequences to end generation

**Advanced:**
- `stream` (boolean) - Real-time streaming responses
- `format` (string) - Output format (e.g., 'json')
- `system` (string) - System message for role definition
- `images` (array) - Base64-encoded images for multimodal models
- `keep_alive` (string/int) - Model persistence duration

### Embedding Features
The Embedding layer supports:

**Request Options:**
- `model` (string) - Embedding model name (e.g., `nomic-embed-text:latest`)
- `input` (string) - Text to generate embeddings for
- `options` (array) - Additional model parameters
- `keep_alive` (string/int) - Model persistence duration

**Response Data:**
- `embeddings` (array) - Array of embedding vectors (supports batch processing)
- `dimension` (int) - Vector dimension (e.g., 768)
- `model` (string) - Model name used
- Performance metrics: `total_duration`, `load_duration`, `prompt_eval_count`

**Methods:**
- `getEmbedding()` - Get first embedding vector (single text)
- `getEmbeddings()` - Get all embedding vectors (batch processing)
- `getDimension()` - Get vector dimension
- `getCount()` - Get number of embeddings

## Development

The library includes comprehensive development tools:

```bash
# Run tests
composer test

# Check code style
composer check-style

# Fix code style issues
composer fix-style

# Run static analysis
composer analyze

# Run all quality checks
composer check
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
