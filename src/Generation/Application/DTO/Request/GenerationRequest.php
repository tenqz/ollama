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
     * @var bool Whether to stream the response
     */
    private $stream = false;

    /**
     * @var bool|null Whether the model should "think" before responding
     */
    private $think;

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
     * @param bool $stream
     * @return self
     */
    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * @return bool
     */
    public function getStream(): bool
    {
        return $this->stream;
    }

    /**
     * @param bool|null $think Enable or disable thinking mode for supported models
     * @return self
     */
    public function setThink(?bool $think): self
    {
        $this->think = $think;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getThink(): ?bool
    {
        return $this->think;
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
            'stream' => $this->stream,
        ];

        // Add prompt only if it's set
        if ($this->prompt !== null) {
            $result['prompt'] = $this->prompt;
        }

        // Add think only if it's set (null means omit)
        if ($this->think !== null) {
            $result['think'] = $this->think;
        }

        return $result;
    }
}
