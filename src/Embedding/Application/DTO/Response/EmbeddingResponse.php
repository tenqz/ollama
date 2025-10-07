<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Embedding\Application\DTO\Response;

use Tenqz\Ollama\Embedding\Domain\Exception\EmbeddingException;

/**
 * Data Transfer Object for embedding generation response.
 */
class EmbeddingResponse
{
    /**
     * @var array<int, float[]> Generated embedding vectors (supports batch processing)
     */
    private $embeddings;

    /**
     * @var string|null Model name used for embedding generation
     */
    private $model;

    /**
     * @var int|null Total time spent generating the embedding (nanoseconds)
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
     * @param array<int, float[]> $embeddings Generated embedding vectors
     */
    public function __construct(array $embeddings)
    {
        $this->embeddings = $embeddings;
    }

    /**
     * Get all embedding vectors (for batch processing).
     *
     * @return array<int, float[]>
     */
    public function getEmbeddings(): array
    {
        return $this->embeddings;
    }

    /**
     * Get first embedding vector (for single text processing).
     *
     * @return float[]
     * @throws EmbeddingException When no embeddings are available
     */
    public function getEmbedding(): array
    {
        if (empty($this->embeddings)) {
            throw new EmbeddingException('No embeddings available in response');
        }

        return $this->embeddings[0];
    }

    /**
     * Get embedding vector dimension.
     *
     * @return int Dimension of the embedding vector (0 if no embeddings)
     */
    public function getDimension(): int
    {
        if (empty($this->embeddings) || empty($this->embeddings[0])) {
            return 0;
        }

        return count($this->embeddings[0]);
    }

    /**
     * Get count of embedding vectors (for batch processing).
     *
     * @return int Number of embedding vectors
     */
    public function getCount(): int
    {
        return count($this->embeddings);
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
     * Convert response to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'embeddings' => $this->embeddings,
        ];

        if ($this->model !== null) {
            $result['model'] = $this->model;
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

        return $result;
    }
}
