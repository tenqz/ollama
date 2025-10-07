<p align="center">
<img src="logo.png" alt="Ollama PHP Client Library" width="200">
</p>

<h1 align="center">Ollama PHP Client Library</h1>

<p align="center">
<span style="font-size: 1.2em;">Documentation for version v0.5.0</span>
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
- **Comprehensive Ollama API support** including text generation with advanced options
- **Type-safe request and response handling** with full DTO support
- **Advanced generation options** including temperature, top-k, top-p, repetition penalty, and more
- **Multimodal support** for image inputs with base64-encoded images
- **Streaming support** for real-time text generation
- **Flexible configuration** with customizable timeouts and connection settings
- **PSR standards compliance** with proper interfaces and abstractions
- **Comprehensive test coverage** with unit tests and quality assurance
- **Easy-to-use examples** with CLI interface and environment variable support

## Installation

You can install the package via composer:

```bash
composer require tenqz/ollama
```

### Examples

Runnable examples are available in the `examples/` directory for quick local testing with Ollama:

- **`examples/generate.php`** — Comprehensive text generation example with:
  - Configurable host/port/timeout settings
  - CLI argument parsing with environment variable fallbacks
  - Optional metadata output for debugging
  - Error handling and validation
  - Support for all generation options and features

**Usage:**
```bash
# Basic usage
php examples/generate.php --model=llama3.2 --prompt="What is AI?"

# With advanced options
php examples/generate.php --model=llama3.2 --prompt="Write a story" --show-meta

# Using environment variables
OLLAMA_MODEL=llama3.2 OLLAMA_PROMPT="Explain quantum computing" php examples/generate.php
```

**Available CLI options:**
- `--model` - Model name (required)
- `--prompt` - Text prompt (required)
- `--host` - Ollama server host (default: localhost)
- `--port` - Ollama server port (default: 11434)
- `--timeout` - Request timeout in seconds (default: 120)
- `--show-meta` - Display response metadata

**Environment variables (fallbacks):**
- `OLLAMA_MODEL`, `OLLAMA_PROMPT`, `OLLAMA_HOST`, `OLLAMA_PORT`, `OLLAMA_TIMEOUT`

**Prerequisites:**
- Running Ollama server (default `http://localhost:11434`)
- Target model available on the server (e.g., `llama3.2`)
- PHP 7.2+ with cURL and JSON extensions
- Dependencies installed: `composer install`

## Usage

### Basic Text Generation

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

### Advanced Generation with Options

```php
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationOptions;

// Create a request with advanced options
$request = new GenerationRequest('llama3.2');
$request->setPrompt('Write a creative story about a robot');
$request->setSystem('You are a creative writing assistant. Write engaging, original stories.');

// Configure generation options
$options = new GenerationOptions();
$options->setTemperature(0.8);           // More creative responses
$options->setTopK(40);                  // Limit vocabulary diversity
$options->setTopP(0.9);                 // Nucleus sampling
$options->setNumPredict(500);           // Limit response length
$options->setRepeatPenalty(1.1);        // Reduce repetition
$options->setStop(['END', 'The End']);   // Stop sequences

$request->setOptions($options);

$response = $apiClient->generate($request);
echo $response->getResponse();
```

### Streaming Generation

```php
// Enable streaming for real-time text generation
$request = new GenerationRequest('llama3.2');
$request->setPrompt('Explain quantum computing step by step');
$request->setStream(true);

$response = $apiClient->generate($request);
echo $response->getResponse(); // Streams the response as it's generated
```

### Multimodal Generation (Images)

```php
// For models that support images (like llava)
$request = new GenerationRequest('llava');
$request->setPrompt('Describe what you see in this image');

// Add base64-encoded images
$request->setImages([
    base64_encode(file_get_contents('path/to/image.jpg'))
]);

$response = $apiClient->generate($request);
echo $response->getResponse();
```

### JSON Format Output

```php
// Request structured JSON output
$request = new GenerationRequest('llama3.2');
$request->setPrompt('Generate a JSON object with user information');
$request->setFormat('json');

$response = $apiClient->generate($request);
$jsonData = json_decode($response->getResponse(), true);
```

### Custom Templates and Context

```php
// Use custom templates and context
$request = new GenerationRequest('llama3.2');
$request->setPrompt('Continue this conversation');
$request->setTemplate('{{ .Prompt }}');
$request->setContext([1, 2, 3, 4, 5]); // Previous context tokens
$request->setKeepAlive('5m'); // Keep model loaded for 5 minutes

$response = $apiClient->generate($request);
echo $response->getResponse();
```

## Architecture

The library follows Domain-Driven Design principles with a clear separation of concerns across multiple layers:

### Transport Layer
- **`TransportClientInterface`** - Interface for HTTP clients with GET/POST methods
- **`ResponseInterface`** - Interface for API responses with status and data access
- **`CurlTransportClient`** - cURL implementation with configurable timeouts and headers
- **`JsonResponse`** - JSON response implementation with data parsing

### Generation Layer
- **`GenerationRequest`** - Comprehensive request DTO with support for:
  - Basic prompts and system messages
  - Advanced options (temperature, top-k, top-p, etc.)
  - Multimodal inputs (base64-encoded images)
  - Streaming and context management
  - Custom templates and output formats
- **`GenerationOptions`** - Fine-grained control over model behavior:
  - Temperature, top-k, top-p, min-p sampling
  - Repetition penalty and context window size
  - Stop sequences and prediction limits
  - Seed for deterministic outputs
- **`GenerationResponse`** - Response DTO with generated text and metadata
- **`GenerationClientInterface`** - Client interface for generation operations
- **`OllamaGenerationClient`** - Implementation with error handling and response transformation

### Shared Layer
- **`OllamaServerConfig`** - Server configuration with host, port, and URL building
- **`OllamaApiEndpoints`** - API endpoint constants for maintainability
- Cross-cutting concerns and utilities used across domains

## Generation Options

The library supports comprehensive generation options for fine-tuning model behavior:

### Sampling Parameters
- **`temperature`** (0.0-1.0) - Controls randomness (higher = more creative)
- **`top_k`** (1-100) - Limits vocabulary diversity 
- **`top_p`** (0.0-1.0) - Nucleus sampling for focused responses
- **`min_p`** (0.0-1.0) - Minimum probability threshold
- **`seed`** (integer) - Deterministic outputs for reproducible results

### Generation Control
- **`num_predict`** (integer) - Maximum tokens to generate (-1 = unlimited)
- **`num_ctx`** (integer) - Context window size (default: 4096)
- **`repeat_penalty`** (float) - Penalty for repetition (1.0 = no penalty)
- **`repeat_last_n`** (integer) - Lookback window for repetition penalty
- **`stop`** (array) - Stop sequences to end generation

### Advanced Features
- **`stream`** (boolean) - Real-time streaming responses
- **`format`** (string) - Output format (e.g., 'json')
- **`template`** (string) - Custom prompt template
- **`system`** (string) - System message for role definition
- **`images`** (array) - Base64-encoded images for multimodal models
- **`context`** (array) - Previous context tokens for continuation
- **`keep_alive`** (string/int) - Model persistence duration

## Requirements

- **PHP 7.2 or higher** (supports PHP 8.0+ features)
- **cURL extension** for HTTP communication
- **JSON extension** for data serialization
- **Ollama server** running locally or remotely

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
