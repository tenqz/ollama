<?php

declare(strict_types=1);

namespace Tests\Unit\Generation\Infrastructure\Client;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse;
use Tenqz\Ollama\Generation\Domain\Exception\GenerationException;
use Tenqz\Ollama\Generation\Infrastructure\Client\OllamaGenerationClient;
use Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints;
use Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;

/**
 * Tests for OllamaGenerationRepository.
 */
class OllamaGenerationClientTest extends TestCase
{
    /**
     * @var TransportClientInterface|MockObject
     */
    private $transportClient;

    /**
     * @var OllamaGenerationClient
     */
    private $client;

    /**
     * @var ResponseInterface|MockObject
     */
    private $mockSuccessfulResponse;

    /**
     * @var array<string, mixed>
     */
    private $fullResponseData;

    /**
     * Set up test environment before each test.
     */
    protected function setUp(): void
    {
        $this->transportClient = $this->createMock(TransportClientInterface::class);
        $this->client = new OllamaGenerationClient($this->transportClient);

        $this->fullResponseData = [
            'model'                => 'test-model',
            'created_at'           => '2023-08-04T19:22:45.499127Z',
            'response'             => 'Generated response text',
            'done'                 => true,
            'context'              => [1, 2, 3],
            'total_duration'       => 5043500667,
            'load_duration'        => 5025959,
            'prompt_eval_count'    => 26,
            'prompt_eval_duration' => 325953000,
            'eval_count'           => 290,
            'eval_duration'        => 4709213000,
        ];

        $this->mockSuccessfulResponse = $this->createMock(ResponseInterface::class);
        $this->mockSuccessfulResponse->method('isSuccessful')->willReturn(true);
        $this->mockSuccessfulResponse->method('getData')->willReturn($this->fullResponseData);
    }

    /**
     * Ensures client issues POST with expected payload to generation endpoint.
     * Why: validates contract between application layer and transport.
     */
    public function testGenerateMakesCorrectRequest(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');
        $request->setPrompt('Hello world');

        // Expectations
        $this->transportClient
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo(OllamaApiEndpoints::GENERATE),
                $this->equalTo($request->toArray())
            )
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $this->client->generate($request);
    }

    /**
     * Ensures client returns a GenerationResponse object.
     * Why: enforces type contract of client API.
     */
    public function testGenerateReturnsGenerationResponseInstance(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertInstanceOf(GenerationResponse::class, $result);
    }

    /**
     * Ensures response text is propagated from API data.
     * Why: response content must be available to callers.
     */
    public function testGenerateReturnsCorrectResponseText(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertEquals('Generated response text', $result->getResponse());
    }

    /**
     * Ensures model metadata is propagated from API data.
     * Why: callers may depend on model identifier.
     */
    public function testGenerateReturnsCorrectModel(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertEquals('test-model', $result->getModel());
    }

    /**
     * Ensures created_at timestamp is propagated from API data.
     * Why: callers may rely on generation timestamp.
     */
    public function testGenerateReturnsCorrectCreatedAt(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertEquals('2023-08-04T19:22:45.499127Z', $result->getCreatedAt());
    }

    /**
     * Ensures each extended field is mapped correctly from API data.
     * Why: preserve full response metadata for callers, AAA one assert per test.
     *
     * @dataProvider providerExtendedFieldsMapping
     */
    public function testGenerateMapsSingleExtendedField(string $getter, $expected): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertSame($expected, $result->{$getter}());
    }

    /**
     * Data provider for single extended field mapping assertions.
     *
     * @return array<int, array{0:string,1:mixed}>
     */
    public function providerExtendedFieldsMapping(): array
    {
        return [
            ['getDone', true],
            ['getContext', [1, 2, 3]],
            ['getTotalDuration', 5043500667],
            ['getLoadDuration', 5025959],
            ['getPromptEvalCount', 26],
            ['getPromptEvalDuration', 325953000],
            ['getEvalCount', 290],
            ['getEvalDuration', 4709213000],
        ];
    }

    /**
     * Ensures HTTP error responses raise GenerationException.
     * Why: callers must handle non-2xx statuses explicitly.
     */
    public function testGenerateThrowsExceptionOnHttpError(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(400);
        $mockResponse->method('getBody')->willReturn('Bad request');

        $this->transportClient
            ->expects($this->once())
            ->method('post')
            ->willReturn($mockResponse);

        // Assert
        $this->expectException(GenerationException::class);

        // Act
        $this->client->generate($request);
    }

    /**
     * Ensures HTTP error message is informative for troubleshooting.
     * Why: better DX for error handling.
     */
    public function testGenerateThrowsExceptionWithCorrectMessageOnHttpError(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(400);
        $mockResponse->method('getBody')->willReturn('Bad request');

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert
        $this->expectExceptionMessage('Generation failed with status code 400');

        // Act
        $this->client->generate($request);
    }

    /**
     * Ensures transport errors raise GenerationException.
     * Why: unify error surface for application layer.
     */
    public function testGenerateThrowsExceptionOnTransportError(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->expects($this->once())
            ->method('post')
            ->willThrowException(new TransportException('Connection failed'));

        // Assert
        $this->expectException(GenerationException::class);

        // Act
        $this->client->generate($request);
    }

    /**
     * Ensures transport error message is preserved in GenerationException.
     * Why: aid debugging of network issues.
     */
    public function testGenerateThrowsExceptionWithCorrectMessageOnTransportError(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willThrowException(new TransportException('Connection failed'));

        // Assert
        $this->expectExceptionMessage('Transport error during text generation: Connection failed');

        // Act
        $this->client->generate($request);
    }

    /**
     * Ensures minimal API response (only response) is handled.
     * Why: metadata may be omitted by server.
     */
    public function testGenerateHandlesMinimalResponseData(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $responseData = [
            'response' => 'Generated response text',
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertEquals('Generated response text', $result->getResponse());
    }

    /**
     * Ensures model is null when absent in API response.
     * Why: DTO should reflect missing metadata correctly.
     */
    public function testGenerateHandlesResponseWithoutModel(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $responseData = [
            'response' => 'Generated response text',
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertNull($result->getModel());
    }

    /**
     * Ensures created_at is null when absent in API response.
     * Why: DTO should reflect missing metadata correctly.
     */
    public function testGenerateHandlesResponseWithoutCreatedAt(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $responseData = [
            'response' => 'Generated response text',
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->generate($request);

        // Assert
        $this->assertNull($result->getCreatedAt());
    }
}
