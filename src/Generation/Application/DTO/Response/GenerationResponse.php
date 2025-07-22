<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Application\DTO\Response;

/**
 * Data Transfer Object for text generation response.
 */
class GenerationResponse
{
    /**
     * @var string Generated text response
     */
    private $response;

    /**
     * @var string|null Model name used for generation
     */
    private $model;

    /**
     * @var string|null Creation timestamp
     */
    private $createdAt;

    /**
     * @param string $response Generated text response
     */
    public function __construct(string $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $model
     * @return self
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Convert response to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'response' => $this->response,
        ];

        if ($this->model !== null) {
            $result['model'] = $this->model;
        }

        if ($this->createdAt !== null) {
            $result['created_at'] = $this->createdAt;
        }

        return $result;
    }
}
