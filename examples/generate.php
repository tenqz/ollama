<?php

declare(strict_types=1);

use Tenqz\Ollama\Examples\GenerateOptions;
use Tenqz\Ollama\Examples\GenerateRunner;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Example: Basic text generation with Ollama.
 *
 * Requirements:
 * - Running Ollama server (default http://localhost:11434)
 * - Model available on the server (e.g., "llama3.2")
 * - Dependencies installed: composer install
 *
 * Usage:
 *   php examples/generate.php --model=llama3.2 --prompt="What is AI?" [--host=localhost] [--port=11434] [--timeout=120] [--show-meta]
 *
 * Environment variables (fallbacks if CLI args are omitted):
 *   OLLAMA_MODEL, OLLAMA_PROMPT, OLLAMA_HOST, OLLAMA_PORT, OLLAMA_TIMEOUT
 *
 * Examples:
 *   php examples/generate.php --prompt="Explain transformers briefly."
 *   OLLAMA_PROMPT="Explain transformers" php examples/generate.php --model=llama3.2 --show-meta
 */

const EXIT_OK = 0;
const EXIT_INVALID_ARGS = 1;
const EXIT_RUNTIME_ERROR = 2;

/**
 * Print usage help.
 */
function printUsage(): void
{
    fwrite(STDOUT, GenerateOptions::usage() . "\n");
}

/**
 * Convert string env to int with fallback.
 *
 * @param string|null $value
 * @param int         $default
 */
// Int conversion is encapsulated inside GenerateOptions

/**
 * Application entrypoint.
 */
function main(): int
{
    $options = GenerateOptions::fromCli();

    if ($options->isHelpRequested()) {
        printUsage();
        return EXIT_OK;
    }

    if (!$options->isValid()) {
        fwrite(STDERR, "Missing prompt. Provide --prompt or OLLAMA_PROMPT.\n\n");
        printUsage();
        return EXIT_INVALID_ARGS;
    }

    try {
        (new GenerateRunner())->run($options);

        return EXIT_OK;
    } catch (TransportException $e) {
        fwrite(STDERR, 'Transport error: ' . $e->getMessage() . "\n");
        return EXIT_RUNTIME_ERROR;
    } catch (\Throwable $e) {
        fwrite(STDERR, 'Unexpected error: ' . $e->getMessage() . "\n");
        return EXIT_RUNTIME_ERROR;
    }
}

exit(main());
