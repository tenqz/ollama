<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Embedding\Application\DTO\Request;

/**
 * Data Transfer Object for embedding generation request.
 */
class EmbeddingRequest
{
    /**
     * @var string Model name
     */
    private $model;

    /**
     * @var string Text to generate embeddings for
     */
    private $prompt;

    /**
     * @var string|int|null How long to keep the model loaded after the request (e.g., "5m")
     */
    private $keepAlive;

    /**
     * @var array<string, mixed>|null Additional model parameters
     */
    private $options;

    /**
     * @param string $model Model name to use for embedding generation
     * @param string $prompt Text to generate embeddings for
     */
    public function __construct(string $model, string $prompt)
    {
        $this->model = $model;
        $this->prompt = $prompt;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * @param string|int|null $keepAlive
     * @return self
     */
    public function setKeepAlive($keepAlive): self
    {
        $this->keepAlive = $keepAlive;

        return $this;
    }

    /**
     * @return string|int|null
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * @param array<string, mixed>|null $options
     * @return self
     */
    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * Convert request to array for API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'model'  => $this->model,
            'prompt' => $this->prompt,
        ];

        // Add keep_alive only if it's set
        if ($this->keepAlive !== null) {
            $result['keep_alive'] = $this->keepAlive;
        }

        // Add options only if set and non-empty
        if (is_array($this->options) && $this->options !== []) {
            $result['options'] = $this->options;
        }

        return $result;
    }
}
