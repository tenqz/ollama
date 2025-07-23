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
     * Test constructor sets model name correctly.
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
     * Test constructor initializes prompt to null.
     */
    public function testConstructorSetsPromptToNull(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getPrompt(), 'Prompt should be null by default');
    }

    /**
     * Test constructor initializes stream to false.
     */
    public function testConstructorSetsStreamToFalse(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertFalse($request->getStream(), 'Stream should be false by default');
    }

    /**
     * Test setPrompt returns self for fluent interface.
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
     * Test setStream returns self for fluent interface.
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
     * Test setPrompt stores value correctly.
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
     * Test setStream stores value correctly.
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
     * Test setting prompt to null.
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
     * Test toArray with model only.
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
     * Test toArray with model and prompt.
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
     * Test toArray with model, prompt and stream enabled.
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
