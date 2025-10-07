<?php

declare(strict_types=1);

namespace Tests\Unit\Embedding\Infrastructure\Client;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Embedding\Application\DTO\Request\EmbeddingRequest;
use Tenqz\Ollama\Embedding\Application\DTO\Response\EmbeddingResponse;
use Tenqz\Ollama\Embedding\Domain\Exception\EmbeddingException;
use Tenqz\Ollama\Embedding\Infrastructure\Client\OllamaEmbeddingClient;
use Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints;
use Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;

/**
 * Tests for OllamaEmbeddingClient.
 */
class OllamaEmbeddingClientTest extends TestCase
{
    /**
     * @var TransportClientInterface|MockObject
     */
    private $transportClient;

    /**
     * @var OllamaEmbeddingClient
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
        $this->client = new OllamaEmbeddingClient($this->transportClient);

        // Sample 768-dimensional embedding vector
        $sampleEmbedding = array_fill(0, 768, 0.1);

        $this->fullResponseData = [
            'model'             => 'nomic-embed-text:latest',
            'embeddings'        => [$sampleEmbedding],
            'total_duration'    => 145991785,
            'load_duration'     => 5659447,
            'prompt_eval_count' => 9,
        ];

        $this->mockSuccessfulResponse = $this->createMock(ResponseInterface::class);
        $this->mockSuccessfulResponse->method('isSuccessful')->willReturn(true);
        $this->mockSuccessfulResponse->method('getData')->willReturn($this->fullResponseData);
    }

    /**
     * Clean up after test run (symmetry with setUp).
     */
    protected function tearDown(): void
    {
        $this->transportClient = null;
        $this->client = null;
        $this->mockSuccessfulResponse = null;
    }

    /**
     * Ensures client issues POST to correct endpoint.
     * Why: validates API endpoint contract.
     */
    public function testEmbedCallsCorrectEndpoint(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Expectations
        $this->transportClient
            ->expects($this->once())
            ->method('post')
            ->with($this->equalTo(OllamaApiEndpoints::EMBED), $this->anything())
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $this->client->embed($request);
    }

    /**
     * Ensures client sends correct request payload.
     * Why: validates request transformation.
     */
    public function testEmbedSendsCorrectPayload(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Expectations
        $this->transportClient
            ->expects($this->once())
            ->method('post')
            ->with($this->anything(), $this->equalTo($request->toArray()))
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $this->client->embed($request);
    }

    /**
     * Ensures client returns EmbeddingResponse instance.
     * Why: enforces type contract of client API.
     */
    public function testEmbedReturnsEmbeddingResponseInstance(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertInstanceOf(EmbeddingResponse::class, $result);
    }

    /**
     * Ensures embedding vector is an array.
     * Why: embedding must be array type.
     */
    public function testEmbedReturnsEmbeddingAsArray(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertIsArray($result->getEmbedding());
    }

    /**
     * Ensures embedding vector has correct dimension.
     * Why: validates vector size from API.
     */
    public function testEmbedReturnsCorrectEmbeddingDimension(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertCount(768, $result->getEmbedding());
    }

    /**
     * Ensures model metadata is propagated from API data.
     * Why: callers may depend on model identifier.
     */
    public function testEmbedReturnsCorrectModel(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertEquals('nomic-embed-text:latest', $result->getModel());
    }

    /**
     * Ensures each extended field is mapped correctly from API data.
     * Why: preserve full response metadata for callers, one assert per test.
     *
     * @dataProvider providerExtendedFieldsMapping
     */
    public function testEmbedMapsSingleExtendedField(string $getter, $expected): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->client->embed($request);

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
            ['getTotalDuration', 145991785],
            ['getLoadDuration', 5659447],
            ['getPromptEvalCount', 9],
        ];
    }

    /**
     * Ensures HTTP error responses raise EmbeddingException.
     * Why: callers must handle non-2xx statuses explicitly.
     */
    public function testEmbedThrowsExceptionOnHttpError(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(400);
        $mockResponse->method('getBody')->willReturn('Bad request');

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $this->client->embed($request);
    }

    /**
     * Ensures HTTP error message contains status code.
     * Why: better DX for error handling.
     */
    public function testEmbedExceptionMessageContainsStatusCode(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(400);
        $mockResponse->method('getBody')->willReturn('Bad request');

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectExceptionMessage('Embedding generation failed with status code 400');
        $this->client->embed($request);
    }

    /**
     * Ensures HTTP error message contains response body.
     * Why: aid debugging of API errors.
     */
    public function testEmbedExceptionMessageContainsResponseBody(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(false);
        $mockResponse->method('getStatusCode')->willReturn(400);
        $mockResponse->method('getBody')->willReturn('Bad request');

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectExceptionMessage('Bad request');
        $this->client->embed($request);
    }

    /**
     * Ensures transport errors raise EmbeddingException.
     * Why: unify error surface for application layer.
     */
    public function testEmbedThrowsExceptionOnTransportError(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willThrowException(new TransportException('Connection failed'));

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $this->client->embed($request);
    }

    /**
     * Ensures transport error message is preserved in EmbeddingException.
     * Why: aid debugging of network issues.
     */
    public function testEmbedExceptionPreservesTransportErrorMessage(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willThrowException(new TransportException('Connection failed'));

        // Assert & Act
        $this->expectExceptionMessage('Transport error during embedding generation: Connection failed');
        $this->client->embed($request);
    }

    /**
     * Ensures minimal API response with only embeddings is handled.
     * Why: metadata may be omitted by server.
     */
    public function testEmbedHandlesMinimalResponse(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $sampleEmbedding = array_fill(0, 768, 0.1);
        $responseData = ['embeddings' => [$sampleEmbedding]];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertCount(768, $result->getEmbedding());
    }

    /**
     * Ensures model is null when absent in API response.
     * Why: DTO should reflect missing metadata correctly.
     */
    public function testEmbedReturnsNullModelWhenAbsentInResponse(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $sampleEmbedding = array_fill(0, 768, 0.1);
        $responseData = ['embeddings' => [$sampleEmbedding]];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertNull($result->getModel());
    }

    /**
     * Ensures each optional metadata field is null when absent.
     * Why: DTO should reflect missing metadata correctly.
     *
     * @dataProvider providerOptionalFieldsNull
     */
    public function testEmbedReturnsNullForAbsentMetadata(string $getter): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $sampleEmbedding = array_fill(0, 768, 0.1);
        $responseData = ['embeddings' => [$sampleEmbedding]];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertNull($result->{$getter}());
    }

    /**
     * Data provider for optional metadata fields that should be null when absent.
     *
     * @return array<int, array{0:string}>
     */
    public function providerOptionalFieldsNull(): array
    {
        return [
            ['getModel'],
            ['getTotalDuration'],
            ['getLoadDuration'],
            ['getPromptEvalCount'],
        ];
    }

    /**
     * Ensures exception is thrown when embeddings field is missing.
     * Why: embeddings is a required field in response.
     */
    public function testEmbedThrowsExceptionWhenEmbeddingsMissing(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['model' => 'nomic-embed-text:latest'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $this->client->embed($request);
    }

    /**
     * Ensures exception message is correct when embeddings field is missing.
     * Why: clear error messages for debugging.
     */
    public function testEmbedExceptionMessageWhenEmbeddingsMissing(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['model' => 'nomic-embed-text:latest'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectExceptionMessage('API response is missing required "embeddings" field');
        $this->client->embed($request);
    }

    /**
     * Ensures exception is thrown when embeddings field is not an array.
     * Why: embeddings must be array type.
     */
    public function testEmbedThrowsExceptionWhenEmbeddingsIsNotArray(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['embeddings' => 'not an array'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $this->client->embed($request);
    }

    /**
     * Ensures exception message is correct when embeddings is not an array.
     * Why: clear error messages for type validation.
     */
    public function testEmbedExceptionMessageWhenEmbeddingsIsNotArray(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['embeddings' => 'not an array'];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Assert & Act
        $this->expectExceptionMessage('API response "embeddings" field must be an array');
        $this->client->embed($request);
    }

    /**
     * Ensures batch processing returns correct embeddings count.
     * Why: API returns array of arrays for batch processing.
     */
    public function testEmbedReturnsBatchEmbeddingsCount(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $embedding1 = array_fill(0, 768, 0.1);
        $embedding2 = array_fill(0, 768, 0.2);
        $responseData = [
            'embeddings' => [$embedding1, $embedding2],
            'model'      => 'nomic-embed-text:latest',
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertCount(2, $result->getEmbeddings());
    }

    /**
     * Ensures batch processing getCount returns correct value.
     * Why: validates count method for batch processing.
     */
    public function testEmbedReturnsBatchEmbeddingsCountViaGetter(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $embedding1 = array_fill(0, 768, 0.1);
        $embedding2 = array_fill(0, 768, 0.2);
        $responseData = [
            'embeddings' => [$embedding1, $embedding2],
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertEquals(2, $result->getCount());
    }

    /**
     * Ensures unexpected exceptions are wrapped in EmbeddingException.
     * Why: unified exception handling for all error types.
     */
    public function testEmbedWrapsUnexpectedExceptionsAsEmbeddingException(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $this->client->embed($request);
    }

    /**
     * Ensures unexpected exception message is preserved.
     * Why: aid debugging of unexpected errors.
     */
    public function testEmbedPreservesUnexpectedExceptionMessage(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $this->transportClient
            ->method('post')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        // Assert & Act
        $this->expectExceptionMessage('Unexpected error during embedding generation: Unexpected error');
        $this->client->embed($request);
    }

    /**
     * Ensures empty embeddings array returns zero count.
     * Why: edge case validation for empty results.
     */
    public function testEmbedReturnsZeroCountForEmptyEmbeddings(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['embeddings' => []];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertEquals(0, $result->getCount());
    }

    /**
     * Ensures empty embeddings array is returned correctly.
     * Why: edge case validation for empty results.
     */
    public function testEmbedReturnsEmptyArrayForNoEmbeddings(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $responseData = ['embeddings' => []];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('isSuccessful')->willReturn(true);
        $mockResponse->method('getData')->willReturn($responseData);

        $this->transportClient
            ->method('post')
            ->willReturn($mockResponse);

        // Act
        $result = $this->client->embed($request);

        // Assert
        $this->assertCount(0, $result->getEmbeddings());
    }
}
