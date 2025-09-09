<?php

declare(strict_types=1);

namespace Tests\Unit\Generation\Application\DTO\Request;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationOptions;
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
     * Ensures constructor initializes suffix as null.
     * Why: suffix is optional and should be omitted when absent.
     */
    public function testConstructorSetsSuffixToNull(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getSuffix(), 'Suffix should be null by default');
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
     * Ensures think is null by default (omitted when not set).
     * Why: think is optional and should be omitted by default.
     */
    public function testConstructorSetsThinkToNullByDefault(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getThink(), 'Think should be null by default');
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
     * Ensures setSuffix returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetSuffixReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setSuffix('    return result');

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
     * Ensures setThink returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetThinkReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setThink(true);

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
     * Ensures setSuffix persists provided value.
     * Why: suffix is part of code-completion use case.
     */
    public function testSetSuffixStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $suffix = '    return result';

        // Act
        $request->setSuffix($suffix);

        // Assert
        $this->assertEquals($suffix, $request->getSuffix());
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
     * Ensures setThink persists provided boolean value.
     * Why: think flag should be stored if provided.
     */
    public function testSetThinkStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $request->setThink(true);

        // Assert
        $this->assertTrue($request->getThink());
    }

    /**
     * Ensures think can be reset to null.
     * Why: optional parameter should be nullable.
     */
    public function testSetThinkToNull(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setThink(true);

        // Act
        $request->setThink(null);

        // Assert
        $this->assertNull($request->getThink(), 'Think should be nullable');
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
     * Ensures suffix can be reset to null.
     * Why: suffix is optional and removable.
     */
    public function testSetSuffixToNull(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setSuffix('    return result');

        // Act
        $request->setSuffix(null);

        // Assert
        $this->assertNull($request->getSuffix(), 'Suffix should be nullable');
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
     * Ensures toArray includes suffix when set.
     */
    public function testToArrayIncludesSuffix(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setSuffix('    return result');

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'suffix' => '    return result',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures options object can be set and serialized when non-empty.
     */
    public function testOptionsSerializationWhenNonEmpty(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $options = (new GenerationOptions())
            ->setSeed(42)
            ->setTemperature(0.8)
            ->setStop(["\n", 'user:']);

        $request->setOptions($options);

        $expected = [
            'model'   => 'llama3.2',
            'stream'  => false,
            'options' => [
                'seed'        => 42,
                'temperature' => 0.8,
                'stop'        => ["\n", 'user:'],
            ],
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures empty options are omitted from payload.
     */
    public function testEmptyOptionsAreOmitted(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setOptions(new GenerationOptions());

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

    /**
     * Ensures toArray includes think when set to true.
     */
    public function testToArrayIncludesThinkWhenTrue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setThink(true);

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'think'  => true,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes think when set to false.
     */
    public function testToArrayIncludesThinkWhenFalse(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setThink(false);

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'think'  => false,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes format when set.
     */
    public function testToArrayIncludesFormat(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setFormat('json');

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'format' => 'json',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
