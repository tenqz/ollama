<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Tests\Unit\Generation\Application\DTO\Response;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse;

/**
 * @covers \Tenqz\Ollama\Generation\Application\DTO\Response\GenerationResponse
 */
class GenerationResponseTest extends TestCase
{
    /**
     * Ensures constructor stores the response text.
     * Why: response content is the primary output of generation.
     *
     * @test
     */
    public function testItShouldCreateResponseWithResponseText(): void
    {
        // Arrange
        $responseText = 'Generated text response';

        // Act
        $response = new GenerationResponse($responseText);

        // Assert
        $this->assertSame($responseText, $response->getResponse());
    }

    /**
     * Ensures default model is null.
     * Why: server may omit model in minimal responses.
     *
     * @test
     */
    public function testItShouldHaveNullModelByDefault(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');

        // Act
        $result = $response->getModel();

        // Assert
        $this->assertNull($result);
    }

    /**
     * Ensures default createdAt is null.
     * Why: timestamp may be absent in minimal responses.
     *
     * @test
     */
    public function testItShouldHaveNullCreatedAtByDefault(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');

        // Act
        $result = $response->getCreatedAt();

        // Assert
        $this->assertNull($result);
    }

    /**
     * Ensures model can be assigned and retrieved.
     * Why: metadata is important for tracing model versions.
     *
     * @test
     */
    public function testItShouldSetAndGetModel(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');
        $model = 'llama3.2';

        // Act
        $response->setModel($model);
        $result = $response->getModel();

        // Assert
        $this->assertSame($model, $result);
    }

    /**
     * Ensures setModel returns self for method chaining.
     * Why: fluent setup for DTOs.
     *
     * @test
     */
    public function testItShouldReturnSelfFromSetModel(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');

        // Act
        $result = $response->setModel('model');

        // Assert
        $this->assertSame($response, $result);
    }

    /**
     * Ensures createdAt can be assigned and retrieved.
     * Why: timestamp may be needed by clients.
     *
     * @test
     */
    public function testItShouldSetAndGetCreatedAt(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');
        $createdAt = '2023-08-04T19:22:45.499127Z';

        // Act
        $response->setCreatedAt($createdAt);
        $result = $response->getCreatedAt();

        // Assert
        $this->assertSame($createdAt, $result);
    }

    /**
     * Ensures setCreatedAt returns self for method chaining.
     * Why: fluent setup for DTOs.
     *
     * @test
     */
    public function testItShouldReturnSelfFromSetCreatedAt(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');

        // Act
        $result = $response->setCreatedAt('2023-08-04T19:22:45.499127Z');

        // Assert
        $this->assertSame($response, $result);
    }

    /**
     * Ensures toArray emits only response when metadata is absent.
     * Why: avoid extra fields in minimal payloads.
     *
     * @test
     */
    public function testItShouldConvertToArrayWithOnlyResponse(): void
    {
        // Arrange
        $responseText = 'Generated text response';
        $response = new GenerationResponse($responseText);

        // Act
        $result = $response->toArray();

        // Assert
        $this->assertSame(['response' => $responseText], $result);
    }

    /**
     * Ensures toArray includes model key when set.
     * Why: preserves model metadata in payloads.
     *
     * @test
     */
    public function testItShouldIncludeModelKeyInArrayWhenSet(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');
        $response->setModel('llama3.2');

        // Act
        $result = $response->toArray();

        // Assert
        $this->assertArrayHasKey('model', $result);
    }

    /**
     * Ensures toArray contains correct model value.
     * Why: validates metadata integrity.
     *
     * @test
     */
    public function testItShouldIncludeCorrectModelValueInArray(): void
    {
        // Arrange
        $model = 'llama3.2';
        $response = new GenerationResponse('Generated text');
        $response->setModel($model);

        // Act
        $result = $response->toArray();

        // Assert
        $this->assertSame($model, $result['model']);
    }

    /**
     * Ensures toArray includes created_at key when set.
     * Why: timestamp should be serialized when present.
     *
     * @test
     */
    public function testItShouldIncludeCreatedAtKeyInArrayWhenSet(): void
    {
        // Arrange
        $response = new GenerationResponse('Generated text');
        $response->setCreatedAt('2023-08-04T19:22:45.499127Z');

        // Act
        $result = $response->toArray();

        // Assert
        $this->assertArrayHasKey('created_at', $result);
    }

    /**
     * Ensures toArray contains correct created_at value.
     * Why: validates timestamp integrity in payload.
     *
     * @test
     */
    public function testItShouldIncludeCorrectCreatedAtValueInArray(): void
    {
        // Arrange
        $createdAt = '2023-08-04T19:22:45.499127Z';
        $response = new GenerationResponse('Generated text');
        $response->setCreatedAt($createdAt);

        // Act
        $result = $response->toArray();

        // Assert
        $this->assertSame($createdAt, $result['created_at']);
    }
}
