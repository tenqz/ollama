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
     * Tests that the response text is correctly set during construction.
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
     * Tests that model property is null by default.
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
     * Tests that createdAt property is null by default.
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
     * Tests that model can be set and retrieved correctly.
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
     * Tests that setModel method returns $this for method chaining.
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
     * Tests that createdAt can be set and retrieved correctly.
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
     * Tests that setCreatedAt method returns $this for method chaining.
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
     * Tests that toArray returns array with only response field when no other fields are set.
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
     * Tests that toArray includes model key when model is set.
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
     * Tests that toArray includes correct model value when model is set.
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
     * Tests that toArray includes created_at key when createdAt is set.
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
     * Tests that toArray includes correct created_at value when createdAt is set.
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
