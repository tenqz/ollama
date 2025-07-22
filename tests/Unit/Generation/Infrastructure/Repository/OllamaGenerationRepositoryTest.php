<?php

declare(strict_types=1);

namespace Tests\Unit\Generation\Infrastructure\Repository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;
use Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse;
use Tenqz\Ollama\Generation\Domain\Exception\GenerationException;
use Tenqz\Ollama\Generation\Infrastructure\Repository\OllamaGenerationRepository;
use Tenqz\Ollama\Shared\Infrastructure\Api\OllamaApiEndpoints;
use Tenqz\Ollama\Transport\Domain\Client\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;

/**
 * Tests for OllamaGenerationRepository.
 */
class OllamaGenerationRepositoryTest extends TestCase
{
    /**
     * @var TransportClientInterface|MockObject
     */
    private $transportClient;

    /**
     * @var OllamaGenerationRepository
     */
    private $repository;

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
        $this->repository = new OllamaGenerationRepository($this->transportClient);

        $this->fullResponseData = [
            'model'      => 'test-model',
            'created_at' => '2023-08-04T19:22:45.499127Z',
            'response'   => 'Generated response text',
        ];

        $this->mockSuccessfulResponse = $this->createMock(ResponseInterface::class);
        $this->mockSuccessfulResponse->method('isSuccessful')->willReturn(true);
        $this->mockSuccessfulResponse->method('getData')->willReturn($this->fullResponseData);
    }

    /**
     * Tests that repository correctly makes POST request with expected parameters.
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
        $this->repository->generate($request);
    }

    /**
     * Tests that repository returns GenerationResponse instance.
     */
    public function testGenerateReturnsGenerationResponseInstance(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->repository->generate($request);

        // Assert
        $this->assertInstanceOf(GenerationResponse::class, $result);
    }

    /**
     * Tests that response text is correctly set in GenerationResponse.
     */
    public function testGenerateReturnsCorrectResponseText(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->repository->generate($request);

        // Assert
        $this->assertEquals('Generated response text', $result->getResponse());
    }

    /**
     * Tests that model is correctly set in GenerationResponse.
     */
    public function testGenerateReturnsCorrectModel(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->repository->generate($request);

        // Assert
        $this->assertEquals('test-model', $result->getModel());
    }

    /**
     * Tests that created_at is correctly set in GenerationResponse.
     */
    public function testGenerateReturnsCorrectCreatedAt(): void
    {
        // Arrange
        $request = new GenerationRequest('test-model');

        $this->transportClient
            ->method('post')
            ->willReturn($this->mockSuccessfulResponse);

        // Act
        $result = $this->repository->generate($request);

        // Assert
        $this->assertEquals('2023-08-04T19:22:45.499127Z', $result->getCreatedAt());
    }

    /**
     * Tests that repository throws exception on HTTP error.
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
        $this->repository->generate($request);
    }

    /**
     * Tests that repository throws exception with correct error message on HTTP error.
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
        $this->repository->generate($request);
    }

    /**
     * Tests that repository throws exception on transport error.
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
        $this->repository->generate($request);
    }

    /**
     * Tests that repository throws exception with correct message on transport error.
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
        $this->repository->generate($request);
    }

    /**
     * Tests that repository handles minimal response data with only response field.
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
        $result = $this->repository->generate($request);

        // Assert
        $this->assertEquals('Generated response text', $result->getResponse());
    }

    /**
     * Tests that model is null when not present in response data.
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
        $result = $this->repository->generate($request);

        // Assert
        $this->assertNull($result->getModel());
    }

    /**
     * Tests that created_at is null when not present in response data.
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
        $result = $this->repository->generate($request);

        // Assert
        $this->assertNull($result->getCreatedAt());
    }
}
