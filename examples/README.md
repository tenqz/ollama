# Examples

This folder contains runnable examples to interact with a local Ollama server.

## Prerequisites
- Running Ollama server (default: `http://localhost:11434`)
- The target model is available on the server (e.g., `llama3.2`)
- PHP 7.2+, extensions: curl, json
- Install dependencies: `composer install`

## `generate.php` — Basic text generation
Run a text generation request and print the response.

Usage:
```bash
php examples/generate.php --model=llama3.2 --prompt="What is AI?" [--host=localhost] [--port=11434] [--timeout=120] [--show-meta]
```

Environment variables (fallbacks when CLI args are omitted):
- `OLLAMA_MODEL`, `OLLAMA_PROMPT`, `OLLAMA_HOST`, `OLLAMA_PORT`, `OLLAMA_TIMEOUT`

Examples:
```bash
php examples/generate.php --prompt="Explain transformers briefly."
OLLAMA_PROMPT="Explain transformers" php examples/generate.php --model=llama3.2 --show-meta
```

Notes:
- Initial requests may take longer due to model loading. Increase `--timeout` if needed.
- Ensure your firewall/network permits access between PHP (WSL) and Ollama on Windows.

## Structure
- `examples/generate.php` — entrypoint
- `examples/lib/GenerateOptions.php` — options parsing and validation
- `examples/lib/GenerateRunner.php` — orchestration of the request/response flow

