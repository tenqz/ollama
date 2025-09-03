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
     * @var bool|null Whether the generation is completed
     */
    private $done;

    /**
     * @var int|null Total time spent generating the response (nanoseconds)
     */
    private $totalDuration;

    /**
     * @var int|null Time spent loading the model (nanoseconds)
     */
    private $loadDuration;

    /**
     * @var int|null Number of tokens in the prompt
     */
    private $promptEvalCount;

    /**
     * @var int|null Time spent evaluating the prompt (nanoseconds)
     */
    private $promptEvalDuration;

    /**
     * @var int|null Number of tokens in the response
     */
    private $evalCount;

    /**
     * @var int|null Time spent generating the response tokens (nanoseconds)
     */
    private $evalDuration;

    /**
     * @var int[]|null Conversation context tokens encoding
     */
    private $context;

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
     * @param bool|null $done
     * @return self
     */
    public function setDone(?bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDone(): ?bool
    {
        return $this->done;
    }

    /**
     * @param int|null $totalDuration
     * @return self
     */
    public function setTotalDuration(?int $totalDuration): self
    {
        $this->totalDuration = $totalDuration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalDuration(): ?int
    {
        return $this->totalDuration;
    }

    /**
     * @param int|null $loadDuration
     * @return self
     */
    public function setLoadDuration(?int $loadDuration): self
    {
        $this->loadDuration = $loadDuration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLoadDuration(): ?int
    {
        return $this->loadDuration;
    }

    /**
     * @param int|null $promptEvalCount
     * @return self
     */
    public function setPromptEvalCount(?int $promptEvalCount): self
    {
        $this->promptEvalCount = $promptEvalCount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPromptEvalCount(): ?int
    {
        return $this->promptEvalCount;
    }

    /**
     * @param int|null $promptEvalDuration
     * @return self
     */
    public function setPromptEvalDuration(?int $promptEvalDuration): self
    {
        $this->promptEvalDuration = $promptEvalDuration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPromptEvalDuration(): ?int
    {
        return $this->promptEvalDuration;
    }

    /**
     * @param int|null $evalCount
     * @return self
     */
    public function setEvalCount(?int $evalCount): self
    {
        $this->evalCount = $evalCount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvalCount(): ?int
    {
        return $this->evalCount;
    }

    /**
     * @param int|null $evalDuration
     * @return self
     */
    public function setEvalDuration(?int $evalDuration): self
    {
        $this->evalDuration = $evalDuration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvalDuration(): ?int
    {
        return $this->evalDuration;
    }

    /**
     * @param int[]|null $context
     * @return self
     */
    public function setContext(?array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getContext(): ?array
    {
        return $this->context;
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

        if ($this->done !== null) {
            $result['done'] = $this->done;
        }

        if ($this->totalDuration !== null) {
            $result['total_duration'] = $this->totalDuration;
        }

        if ($this->loadDuration !== null) {
            $result['load_duration'] = $this->loadDuration;
        }

        if ($this->promptEvalCount !== null) {
            $result['prompt_eval_count'] = $this->promptEvalCount;
        }

        if ($this->promptEvalDuration !== null) {
            $result['prompt_eval_duration'] = $this->promptEvalDuration;
        }

        if ($this->evalCount !== null) {
            $result['eval_count'] = $this->evalCount;
        }

        if ($this->evalDuration !== null) {
            $result['eval_duration'] = $this->evalDuration;
        }

        if ($this->context !== null) {
            $result['context'] = $this->context;
        }

        return $result;
    }
}
