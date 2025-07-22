<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Domain\Repository;

use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse;

/**
 * Repository interface for Ollama API interactions.
 */
interface GenerationRepositoryInterface
{
    /**
     * Performs text generation request to Ollama API.
     *
     * @param GenerationRequest $request Generation request
     *
     * @return GenerationResponse Generated text response with metadata like model, creation time, etc.
     *
     * @throws \Tenqz\Ollama\Generation\Domain\Exception\GenerationException When generation fails
     */
    public function generate(GenerationRequest $request): GenerationResponse;
}
