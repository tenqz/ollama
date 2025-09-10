<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Infrastructure\Client;

use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse;
use Tenqz\Ollama\Generation\Domain\Client\GenerationClientInterface;
use Tenqz\Ollama\Generation\Domain\Exception\GenerationException;
use Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints;
use Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;

/**
 * Implementation of GenerationClientInterface using Ollama API.
 */
class OllamaGenerationClient implements GenerationClientInterface
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
     * Generates text using Ollama API based on provided request.
     *
     * This method sends a request to the Ollama API's generation endpoint
     * and processes the response. It handles all potential error cases and
     * transforms the API response into a domain response object.
     *
     * The method follows these steps:
     * 1. Execute the request to the API
     * 2. Validate the response for error status
     * 3. Transform the response data into a GenerationResponse object
     * 4. Handle any exceptions that occur during this process
     *
     * {@inheritdoc}
     *
     * @throws GenerationException When any error occurs during generation
     */
    public function generate(GenerationRequest $request): GenerationResponse
    {
        try {
            $responseData = $this->executeGenerationRequest($request);

            return $this->createGenerationResponse($responseData);
        } catch (TransportException $e) {
            throw $this->createTransportException($e);
        } catch (\Throwable $e) {
            throw $this->createUnexpectedException($e);
        }
    }

    /**
     * Execute the generation request to the API.
     *
     * @param GenerationRequest $request The request to execute
     *
     * @return array<string, mixed> Response data
     *
     * @throws TransportException When transport error occurs
     * @throws GenerationException When API returns error status
     */
    private function executeGenerationRequest(GenerationRequest $request): array
    {
        $response = $this->transportClient->post(OllamaApiEndpoints::GENERATE, $request->toArray());

        $this->validateResponse($response);

        return $response->getData();
    }

    /**
     * Validate API response.
     *
     * @param ResponseInterface $response The response to validate
     *
     * @throws GenerationException When response is not successful
     */
    private function validateResponse(ResponseInterface $response): void
    {
        if (!$response->isSuccessful()) {
            throw new GenerationException(
                sprintf('Generation failed with status code %d: %s', $response->getStatusCode(), $response->getBody()),
                $response->getStatusCode()
            );
        }
    }

    /**
     * Create GenerationResponse from API response data.
     *
     * @param array<string, mixed> $data Response data
     *
     * @return GenerationResponse
     */
    private function createGenerationResponse(array $data): GenerationResponse
    {
        if (!isset($data['response'])) {
            throw new GenerationException('API response is missing required "response" field');
        }

        $generationResponse = new GenerationResponse($data['response']);

        if (isset($data['model'])) {
            $generationResponse->setModel($data['model']);
        }

        if (isset($data['created_at'])) {
            $generationResponse->setCreatedAt($data['created_at']);
        }

        if (isset($data['done'])) {
            $generationResponse->setDone((bool) $data['done']);
        }

        if (isset($data['context']) && is_array($data['context'])) {
            $generationResponse->setContext($data['context']);
        }

        if (isset($data['total_duration'])) {
            $generationResponse->setTotalDuration((int) $data['total_duration']);
        }

        if (isset($data['load_duration'])) {
            $generationResponse->setLoadDuration((int) $data['load_duration']);
        }

        if (isset($data['prompt_eval_count'])) {
            $generationResponse->setPromptEvalCount((int) $data['prompt_eval_count']);
        }

        if (isset($data['prompt_eval_duration'])) {
            $generationResponse->setPromptEvalDuration((int) $data['prompt_eval_duration']);
        }

        if (isset($data['eval_count'])) {
            $generationResponse->setEvalCount((int) $data['eval_count']);
        }

        if (isset($data['eval_duration'])) {
            $generationResponse->setEvalDuration((int) $data['eval_duration']);
        }

        return $generationResponse;
    }

    /**
     * Create exception for transport errors.
     *
     * @param TransportException $e Original exception
     *
     * @return GenerationException
     */
    private function createTransportException(TransportException $e): GenerationException
    {
        return new GenerationException(
            'Transport error during text generation: ' . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }

    /**
     * Create exception for unexpected errors.
     *
     * @param \Throwable $e Original exception
     *
     * @return GenerationException
     */
    private function createUnexpectedException(\Throwable $e): GenerationException
    {
        return new GenerationException(
            'Unexpected error during text generation: ' . $e->getMessage(),
            0,
            $e
        );
    }
}
