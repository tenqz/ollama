<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Examples;

use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Infrastructure\Client\OllamaGenerationClient;
use Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

/**
 * Coordinates the generation flow for the example.
 */
class GenerateRunner
{
    /**
     * Run generation and print output.
     *
     * @throws TransportException
     */
    public function run(GenerateOptions $options): void
    {
        $apiClient = $this->createClient($options);
        $request = $this->createRequest($options);

        $response = $apiClient->generate($request);

        fwrite(STDOUT, $response->getResponse() . "\n");

        if ($options->shouldShowMeta()) {
            $modelName = $response->getModel();
            $createdAt = $response->getCreatedAt();
            if ($modelName !== null) {
                fwrite(STDOUT, "# model:    {$modelName}\n");
            }
            if ($createdAt !== null) {
                fwrite(STDOUT, "# created:  {$createdAt}\n");
            }
        }
    }

    /**
     * Create an API client with configured transport.
     */
    private function createClient(GenerateOptions $options): OllamaGenerationClient
    {
        $config = new OllamaServerConfig($options->getHost(), $options->getPort());
        $transportClient = new CurlTransportClient($config->getBaseUrl(), [], $options->getTimeout());

        return new OllamaGenerationClient($transportClient);
    }

    /**
     * Create a generation request from provided options.
     */
    private function createRequest(GenerateOptions $options): GenerationRequest
    {
        $request = new GenerationRequest($options->getModel());
        $request->setPrompt($options->getPrompt());

        return $request;
    }
}


