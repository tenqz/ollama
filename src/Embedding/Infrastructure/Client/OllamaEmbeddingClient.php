<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Embedding\Infrastructure\Client;

use Tenqz\Ollama\Embedding\Application\DTO\Request\EmbeddingRequest;
use Tenqz\Ollama\Embedding\Application\DTO\Response\EmbeddingResponse;
use Tenqz\Ollama\Embedding\Domain\Client\EmbeddingClientInterface;
use Tenqz\Ollama\Embedding\Domain\Exception\EmbeddingException;
use Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints;
use Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;

/**
 * Implementation of EmbeddingClientInterface using Ollama API.
 */
class OllamaEmbeddingClient implements EmbeddingClientInterface
{
    /**
     * @var TransportClientInterface Transport client for making HTTP requests
     */
    private $transportClient;

    /**
     * @param TransportClientInterface $transportClient Transport client
     */
    public function __construct(TransportClientInterface $transportClient)
    {
        $this->transportClient = $transportClient;
    }

    /**
     * Generates embedding vector using Ollama API based on provided request.
     *
     * This method sends a request to the Ollama API's embedding endpoint
     * and processes the response. It handles all potential error cases and
     * transforms the API response into a domain response object.
     *
     * The method follows these steps:
     * 1. Execute the request to the API
     * 2. Validate the response for error status
     * 3. Transform the response data into an EmbeddingResponse object
     * 4. Handle any exceptions that occur during this process
     *
     * {@inheritdoc}
     *
     * @throws EmbeddingException When any error occurs during embedding generation
     */
    public function embed(EmbeddingRequest $request): EmbeddingResponse
    {
        try {
            $responseData = $this->executeEmbeddingRequest($request);

            return $this->createEmbeddingResponse($responseData);
        } catch (TransportException $e) {
            throw $this->createTransportException($e);
        } catch (\Throwable $e) {
            throw $this->createUnexpectedException($e);
        }
    }

    /**
     * Execute the embedding request to the API.
     *
     * @param EmbeddingRequest $request The request to execute
     *
     * @return array<string, mixed> Response data
     *
     * @throws TransportException When transport error occurs
     * @throws EmbeddingException When API returns error status
     */
    private function executeEmbeddingRequest(EmbeddingRequest $request): array
    {
        $response = $this->transportClient->post(OllamaApiEndpoints::EMBED, $request->toArray());

        $this->validateResponse($response);

        return $response->getData();
    }

    /**
     * Validate API response.
     *
     * @param ResponseInterface $response The response to validate
     *
     * @throws EmbeddingException When response is not successful
     */
    private function validateResponse(ResponseInterface $response): void
    {
        if (!$response->isSuccessful()) {
            throw new EmbeddingException(
                sprintf('Embedding generation failed with status code %d: %s', $response->getStatusCode(), $response->getBody()),
                $response->getStatusCode()
            );
        }
    }

    /**
     * Create EmbeddingResponse from API response data.
     *
     * @param array<string, mixed> $data Response data
     *
     * @return EmbeddingResponse
     *
     * @throws EmbeddingException When response is missing required fields
     */
    private function createEmbeddingResponse(array $data): EmbeddingResponse
    {
        if (!isset($data['embeddings'])) {
            throw new EmbeddingException('API response is missing required "embeddings" field');
        }

        if (!is_array($data['embeddings'])) {
            throw new EmbeddingException('API response "embeddings" field must be an array');
        }

        $embeddingResponse = new EmbeddingResponse($data['embeddings']);

        if (isset($data['model'])) {
            $embeddingResponse->setModel($data['model']);
        }

        if (isset($data['total_duration'])) {
            $embeddingResponse->setTotalDuration((int) $data['total_duration']);
        }

        if (isset($data['load_duration'])) {
            $embeddingResponse->setLoadDuration((int) $data['load_duration']);
        }

        if (isset($data['prompt_eval_count'])) {
            $embeddingResponse->setPromptEvalCount((int) $data['prompt_eval_count']);
        }

        return $embeddingResponse;
    }

    /**
     * Create exception for transport errors.
     *
     * @param TransportException $e Original exception
     *
     * @return EmbeddingException
     */
    private function createTransportException(TransportException $e): EmbeddingException
    {
        return new EmbeddingException(
            'Transport error during embedding generation: ' . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }

    /**
     * Create exception for unexpected errors.
     *
     * @param \Throwable $e Original exception
     *
     * @return EmbeddingException
     */
    private function createUnexpectedException(\Throwable $e): EmbeddingException
    {
        return new EmbeddingException(
            'Unexpected error during embedding generation: ' . $e->getMessage(),
            0,
            $e
        );
    }
}
