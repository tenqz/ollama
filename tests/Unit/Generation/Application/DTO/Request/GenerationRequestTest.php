<?php

declare(strict_types=1);

namespace Tests\Unit\Generation\Application\DTO\Request;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationRequest;

/**
 * Tests for GenerationRequest DTO.
 */
class GenerationRequestTest extends TestCase
{
    /**
     * Ensures constructor assigns provided model name.
     * Why: request must target an explicit model.
     */
    public function testConstructorSetsModelName(): void
    {
        // Arrange
        $modelName = 'llama3.2';

        // Act
        $request = new GenerationRequest($modelName);

        // Assert
        $this->assertEquals($modelName, $request->getModel());
    }

    /**
     * Ensures constructor initializes prompt as null.
     * Why: prompt is optional and should be omitted when absent.
     */
    public function testConstructorSetsPromptToNull(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getPrompt(), 'Prompt should be null by default');
    }

    /**
     * Ensures constructor initializes stream to false.
     * Why: default behavior is non-streaming.
     */
    public function testConstructorSetsStreamToFalse(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertFalse($request->getStream(), 'Stream should be false by default');
    }

    /**
     * Ensures setPrompt returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetPromptReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setPrompt('Why is the sky blue?');

        // Assert
        $this->assertSame($request, $result, 'Setter should return $this for fluent interface');
    }

    /**
     * Ensures setStream returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetStreamReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setStream(true);

        // Assert
        $this->assertSame($request, $result, 'Setter should return $this for fluent interface');
    }

    /**
     * Ensures setPrompt persists provided value.
     * Why: prompt is core input for generation.
     */
    public function testSetPromptStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $prompt = 'Why is the sky blue?';

        // Act
        $request->setPrompt($prompt);

        // Assert
        $this->assertEquals($prompt, $request->getPrompt());
    }

    /**
     * Ensures setStream persists provided boolean value.
     * Why: streaming flag affects transport behavior.
     */
    public function testSetStreamStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $request->setStream(true);

        // Assert
        $this->assertTrue($request->getStream());
    }

    /**
     * Ensures prompt can be reset to null.
     * Why: prompt is optional and removable.
     */
    public function testSetPromptToNull(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setPrompt('Initial prompt');

        // Act
        $request->setPrompt(null);

        // Assert
        $this->assertNull($request->getPrompt(), 'Prompt should be nullable');
    }

    /**
     * Ensures toArray emits only required fields when no prompt.
     * Why: avoid sending unnecessary keys.
     */
    public function testToArrayWithModelOnly(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes prompt when set.
     * Why: API expects prompt key only when present.
     */
    public function testToArrayWithModelAndPrompt(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setPrompt('Why is the sky blue?');

        $expected = [
            'model'  => 'llama3.2',
            'prompt' => 'Why is the sky blue?',
            'stream' => false,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray reflects stream flag and prompt together.
     * Why: combined flags must serialize consistently.
     */
    public function testToArrayWithModelPromptAndStreamEnabled(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setPrompt('Why is the sky blue?');
        $request->setStream(true);

        $expected = [
            'model'  => 'llama3.2',
            'prompt' => 'Why is the sky blue?',
            'stream' => true,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
