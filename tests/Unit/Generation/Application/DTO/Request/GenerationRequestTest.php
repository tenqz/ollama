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
     * Ensures constructor initializes system as null.
     * Why: system prompt is optional.
     */
    public function testConstructorSetsSystemToNull(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getSystem(), 'System should be null by default');
    }

    /**
     * Ensures constructor initializes template as null.
     * Why: template is optional.
     */
    public function testConstructorSetsTemplateToNull(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getTemplate(), 'Template should be null by default');
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
     * Ensures setSystem persists provided value.
     * Why: system prompt should be stored when provided.
     */
    public function testSetSystemStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $system = 'You are a helpful assistant';

        // Act
        $request->setSystem($system);

        // Assert
        $this->assertEquals($system, $request->getSystem());
    }

    /**
     * Ensures setSystem returns self for fluent chaining.
     */
    public function testSetSystemReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setSystem('System prompt');

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures setTemplate persists provided value.
     */
    public function testSetTemplateStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $template = 'Answer: {{ response }}';

        // Act
        $request->setTemplate($template);

        // Assert
        $this->assertEquals($template, $request->getTemplate());
    }

    /**
     * Ensures setTemplate returns self for fluent chaining.
     */
    public function testSetTemplateReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setTemplate('T: {{ response }}');

        // Assert
        $this->assertSame($request, $result);
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
     * Ensures toArray includes system when set.
     */
    public function testToArrayIncludesSystem(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setSystem('You are a helpful assistant');

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'system' => 'You are a helpful assistant',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes template when set.
     */
    public function testToArrayIncludesTemplate(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setTemplate('Answer: {{ response }}');

        $expected = [
            'model'    => 'llama3.2',
            'stream'   => false,
            'template' => 'Answer: {{ response }}',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures keepAlive is null by default.
     */
    public function testKeepAliveIsNullByDefault(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getKeepAlive());
    }

    /**
     * Ensures setKeepAlive stores provided value (string).
     */
    public function testSetKeepAliveStoresString(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $request->setKeepAlive('5m');

        // Assert
        $this->assertSame('5m', $request->getKeepAlive());
    }

    /**
     * Ensures setKeepAlive stores provided value (int).
     */
    public function testSetKeepAliveStoresInt(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $request->setKeepAlive(300);

        // Assert
        $this->assertSame(300, $request->getKeepAlive());
    }

    /**
     * Ensures setKeepAlive returns self for fluent chaining.
     */
    public function testSetKeepAliveReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setKeepAlive('5m');

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures toArray includes keep_alive when set.
     */
    public function testToArrayIncludesKeepAlive(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setKeepAlive('5m');

        $expected = [
            'model'      => 'llama3.2',
            'stream'     => false,
            'keep_alive' => '5m',
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

    /**
     * Ensures context is null by default.
     */
    public function testContextIsNullByDefault(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getContext());
    }

    /**
     * Ensures setContext stores provided token list.
     */
    public function testSetContextStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $context = [1, 2, 3];

        // Act
        $request->setContext($context);

        // Assert
        $this->assertSame($context, $request->getContext());
    }

    /**
     * Ensures setContext returns self for fluent chaining.
     */
    public function testSetContextReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setContext([1, 2]);

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures toArray includes context when provided.
     */
    public function testToArrayIncludesContext(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $context = [1, 2, 3];
        $request->setContext($context);

        $expected = [
            'model'   => 'llama3.2',
            'stream'  => false,
            'context' => $context,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures empty context array is omitted in toArray.
     */
    public function testEmptyContextArrayIsOmitted(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setContext([]);

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
     * Ensures images is null by default.
     */
    public function testImagesIsNullByDefault(): void
    {
        // Arrange & Act
        $request = new GenerationRequest('llama3.2');

        // Assert
        $this->assertNull($request->getImages());
    }

    /**
     * Ensures setImages stores provided base64 list.
     */
    public function testSetImagesStoresValue(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $images = ['iVBORw0KGgoAAA', 'R0lGODlhAQABAIAAAAUEBA=='];

        // Act
        $request->setImages($images);

        // Assert
        $this->assertSame($images, $request->getImages());
    }

    /**
     * Ensures setImages returns self for fluent chaining.
     */
    public function testSetImagesReturnsSelf(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');

        // Act
        $result = $request->setImages(['abc']);

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures toArray includes images when provided.
     */
    public function testToArrayIncludesImages(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $images = ['iVBORw0KGgoAAA', 'R0lGODlhAQABAIAAAAUEBA=='];
        $request->setImages($images);

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
            'images' => $images,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures empty images array is omitted in toArray.
     */
    public function testEmptyImagesArrayIsOmitted(): void
    {
        // Arrange
        $request = new GenerationRequest('llama3.2');
        $request->setImages([]);

        $expected = [
            'model'  => 'llama3.2',
            'stream' => false,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
