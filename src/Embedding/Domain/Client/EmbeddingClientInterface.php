<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Embedding\Domain\Client;

use Tenqz\Ollama\Embedding\Application\DTO\Request\EmbeddingRequest;
use Tenqz\Ollama\Embedding\Application\DTO\Response\EmbeddingResponse;

/**
 * Client interface for embedding operations.
 */
interface EmbeddingClientInterface
{
    /**
     * Generates embedding vector for provided text.
     *
     * @param EmbeddingRequest $request Embedding request with model and text
     *
     * @return EmbeddingResponse Generated embedding vector with metadata
     *
     * @throws \Tenqz\Ollama\Embedding\Domain\Exception\EmbeddingException When embedding generation fails
     */
    public function embed(EmbeddingRequest $request): EmbeddingResponse;
}
