<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Application\DTO\Request;

/**
 * Data Transfer Object for text generation request.
 */
class GenerationRequest
{
    /**
     * @var string Model name
     */
    private $model;

    /**
     * @var string|null Prompt to generate a response for
     */
    private $prompt;

    /**
     * @param string $model Model name to use for generation
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string|null $prompt
     * @return self
     */
    public function setPrompt(?string $prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    /**
     * Convert request to array for API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'model' => $this->model,
        ];

        // Add prompt only if it's set
        if ($this->prompt !== null) {
            $result['prompt'] = $this->prompt;
        }

        return $result;
    }
}
