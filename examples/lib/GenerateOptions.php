<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Examples;

/**
 * Encapsulates CLI/environment options for the generate example.
 *
 * PHP 7.2 compatible (no typed properties).
 */
class GenerateOptions
{
    /** @var string */
    private $model;

    /** @var string|null */
    private $prompt;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var int */
    private $timeout;

    /** @var bool */
    private $showMeta;

    /** @var bool */
    private $help;

    /**
     * @param string      $model
     * @param string|null $prompt
     * @param string      $host
     * @param int         $port
     * @param int         $timeout
     * @param bool        $showMeta
     * @param bool        $help
     */
    public function __construct($model, $prompt, $host, $port, $timeout, $showMeta, $help)
    {
        $this->model = (string) $model;
        $this->prompt = $prompt === null ? null : (string) $prompt;
        $this->host = (string) $host;
        $this->port = (int) $port;
        $this->timeout = (int) $timeout;
        $this->showMeta = (bool) $showMeta;
        $this->help = (bool) $help;
    }

    /**
     * Parse CLI arguments and environment variables.
     */
    public static function fromCli(): self
    {
        $args = getopt('', ['model:', 'prompt:', 'host::', 'port::', 'timeout::', 'show-meta', 'help']);

        $model = isset($args['model']) ? (string) $args['model'] : (getenv('OLLAMA_MODEL') ?: 'llama3.2');
        $prompt = isset($args['prompt']) ? (string) $args['prompt'] : (getenv('OLLAMA_PROMPT') ?: null);
        $host = isset($args['host']) ? (string) $args['host'] : (getenv('OLLAMA_HOST') ?: 'localhost');
        $port = isset($args['port']) ? (int) $args['port'] : self::toInt(getenv('OLLAMA_PORT') ?: null, 11434);
        $timeout = isset($args['timeout']) ? (int) $args['timeout'] : self::toInt(getenv('OLLAMA_TIMEOUT') ?: null, 120);
        $showMeta = isset($args['show-meta']);
        $help = isset($args['help']);

        return new self($model, $prompt, $host, $port, $timeout, $showMeta, $help);
    }

    /**
     * Validate required options are present.
     */
    public function isValid(): bool
    {
        return $this->prompt !== null && $this->prompt !== '';
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string|null
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return bool
     */
    public function shouldShowMeta()
    {
        return $this->showMeta;
    }

    /**
     * @return bool
     */
    public function isHelpRequested()
    {
        return $this->help;
    }

    /**
     * @return string
     */
    public static function usage()
    {
        return <<<TXT
Usage:
  php examples/generate.php --model=llama3.2 --prompt="What is AI?" [--host=localhost] [--port=11434] [--timeout=120] [--show-meta]

Options:
  --model      Model name (default: env OLLAMA_MODEL or 'llama3.2')
  --prompt     Text prompt (required if OLLAMA_PROMPT is not set)
  --host       Ollama host (default: env OLLAMA_HOST or 'localhost')
  --port       Ollama port (default: env OLLAMA_PORT or 11434)
  --timeout    Request timeout seconds (default: env OLLAMA_TIMEOUT or 120)
  --show-meta  Print model and created_at metadata when available
  --help       Show this help

Notes:
  Ensure the Ollama server is running and the model is available on the server.
TXT;
    }

    /**
     * Convert string env to int with fallback.
     *
     * @param string|null $value
     * @param int         $default
     *
     * @return int
     */
    private static function toInt($value, $default)
    {
        if ($value === null || $value === '') {
            return (int) $default;
        }

        $int = filter_var($value, FILTER_VALIDATE_INT);

        return $int === false ? (int) $default : (int) $int;
    }
}


