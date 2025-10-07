<?php

declare(strict_types=1);

namespace Tests\Unit\Embedding\Application\DTO\Request;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Embedding\Application\DTO\Request\EmbeddingRequest;

/**
 * Tests for EmbeddingRequest DTO.
 */
class EmbeddingRequestTest extends TestCase
{
    /**
     * Ensures constructor assigns provided model name.
     * Why: request must target an explicit model.
     */
    public function testConstructorSetsModelName(): void
    {
        // Arrange
        $modelName = 'nomic-embed-text:latest';
        $input = 'Hello world';

        // Act
        $request = new EmbeddingRequest($modelName, $input);

        // Assert
        $this->assertEquals($modelName, $request->getModel());
    }

    /**
     * Ensures constructor assigns provided input text.
     * Why: input is required for embedding generation.
     */
    public function testConstructorSetsInput(): void
    {
        // Arrange
        $modelName = 'nomic-embed-text:latest';
        $input = 'Hello world';

        // Act
        $request = new EmbeddingRequest($modelName, $input);

        // Assert
        $this->assertEquals($input, $request->getInput());
    }

    /**
     * Ensures constructor initializes keepAlive as null.
     * Why: keepAlive is optional and should be omitted when absent.
     */
    public function testConstructorSetsKeepAliveToNull(): void
    {
        // Arrange & Act
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Assert
        $this->assertNull($request->getKeepAlive());
    }

    /**
     * Ensures constructor initializes options as null.
     * Why: options are optional.
     */
    public function testConstructorSetsOptionsToNull(): void
    {
        // Arrange & Act
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Assert
        $this->assertNull($request->getOptions());
    }

    /**
     * Ensures getModel returns constructor value.
     * Why: model identifier must be retrievable.
     */
    public function testGetModelReturnsConstructorValue(): void
    {
        // Arrange
        $modelName = 'nomic-embed-text:latest';
        $request = new EmbeddingRequest($modelName, 'Hello world');

        // Act
        $result = $request->getModel();

        // Assert
        $this->assertEquals($modelName, $result);
    }

    /**
     * Ensures getInput returns constructor value.
     * Why: input text must be retrievable.
     */
    public function testGetInputReturnsConstructorValue(): void
    {
        // Arrange
        $input = 'Hello world';
        $request = new EmbeddingRequest('nomic-embed-text:latest', $input);

        // Act
        $result = $request->getInput();

        // Assert
        $this->assertEquals($input, $result);
    }

    /**
     * Ensures setKeepAlive returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetKeepAliveReturnsSelf(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Act
        $result = $request->setKeepAlive('5m');

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures setKeepAlive stores string value.
     * Why: keepAlive can be time string.
     */
    public function testSetKeepAliveStoresStringValue(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $keepAlive = '5m';

        // Act
        $request->setKeepAlive($keepAlive);

        // Assert
        $this->assertEquals($keepAlive, $request->getKeepAlive());
    }

    /**
     * Ensures setKeepAlive stores integer value.
     * Why: keepAlive can be seconds as integer.
     */
    public function testSetKeepAliveStoresIntegerValue(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $keepAlive = 300;

        // Act
        $request->setKeepAlive($keepAlive);

        // Assert
        $this->assertSame($keepAlive, $request->getKeepAlive());
    }

    /**
     * Ensures setOptions returns self for fluent chaining.
     * Why: allows concise request configuration.
     */
    public function testSetOptionsReturnsSelf(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Act
        $result = $request->setOptions(['key' => 'value']);

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures setOptions stores provided array.
     * Why: options should be persisted.
     */
    public function testSetOptionsStoresValue(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $options = ['temperature' => 0.8, 'seed' => 42];

        // Act
        $request->setOptions($options);

        // Assert
        $this->assertEquals($options, $request->getOptions());
    }

    /**
     * Ensures setOptions accepts null value.
     * Why: options should be clearable.
     */
    public function testSetOptionsAcceptsNull(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setOptions(['key' => 'value']);

        // Act
        $request->setOptions(null);

        // Assert
        $this->assertNull($request->getOptions());
    }

    /**
     * Ensures toArray emits only required fields by default.
     * Why: avoid sending unnecessary keys.
     */
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        $expected = [
            'model' => 'nomic-embed-text:latest',
            'input' => 'Hello world',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes keepAlive when set as string.
     * Why: API expects keep_alive key only when present.
     */
    public function testToArrayIncludesKeepAliveString(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setKeepAlive('5m');

        $expected = [
            'model'      => 'nomic-embed-text:latest',
            'input'      => 'Hello world',
            'keep_alive' => '5m',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes keepAlive when set as integer.
     * Why: API accepts keep_alive as integer seconds.
     */
    public function testToArrayIncludesKeepAliveInteger(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setKeepAlive(300);

        $expected = [
            'model'      => 'nomic-embed-text:latest',
            'input'      => 'Hello world',
            'keep_alive' => 300,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes options when set.
     * Why: API expects options key only when present.
     */
    public function testToArrayIncludesOptions(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $options = ['temperature' => 0.8, 'seed' => 42];
        $request->setOptions($options);

        $expected = [
            'model'   => 'nomic-embed-text:latest',
            'input'   => 'Hello world',
            'options' => $options,
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray omits empty options array.
     * Why: API expects no options key if empty.
     */
    public function testToArrayOmitsEmptyOptions(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setOptions([]);

        $expected = [
            'model' => 'nomic-embed-text:latest',
            'input' => 'Hello world',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures toArray includes all fields when everything is set.
     * Why: complete payload serialization.
     */
    public function testToArrayIncludesAllFieldsWhenSet(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setKeepAlive('5m');
        $request->setOptions(['temperature' => 0.8]);

        $expected = [
            'model'      => 'nomic-embed-text:latest',
            'input'      => 'Hello world',
            'keep_alive' => '5m',
            'options'    => ['temperature' => 0.8],
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Ensures model name can contain various formats.
     * Why: validates different model naming conventions.
     *
     * @dataProvider providerModelNames
     */
    public function testAcceptsDifferentModelNameFormats(string $modelName): void
    {
        // Act
        $request = new EmbeddingRequest($modelName, 'Hello world');

        // Assert
        $this->assertEquals($modelName, $request->getModel());
    }

    /**
     * Data provider for various model name formats.
     *
     * @return array<int, array{0:string}>
     */
    public function providerModelNames(): array
    {
        return [
            ['nomic-embed-text:latest'],
            ['nomic-embed-text'],
            ['llama2'],
            ['model-name-123'],
            ['org/model:tag'],
        ];
    }

    /**
     * Ensures input text can be of various lengths.
     * Why: validates different text input scenarios.
     *
     * @dataProvider providerInputTexts
     */
    public function testAcceptsDifferentInputTextLengths(string $input): void
    {
        // Act
        $request = new EmbeddingRequest('nomic-embed-text:latest', $input);

        // Assert
        $this->assertEquals($input, $request->getInput());
    }

    /**
     * Data provider for various input text scenarios.
     *
     * @return array<int, array{0:string}>
     */
    public function providerInputTexts(): array
    {
        return [
            ['Short text'],
            ['This is a longer text that might be used for embedding generation in real scenarios'],
            ['Single word'],
            ['Text with special chars: @#$%^&*()'],
            ['Текст на кириллице'],
            [''],  // Empty string
        ];
    }

    /**
     * Ensures fluent interface works with method chaining.
     * Why: validates builder pattern support.
     */
    public function testFluentInterfaceChaining(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');

        // Act
        $result = $request
            ->setKeepAlive('5m')
            ->setOptions(['temperature' => 0.8]);

        // Assert
        $this->assertSame($request, $result);
    }

    /**
     * Ensures keepAlive can be reset to null.
     * Why: optional parameter should be nullable.
     */
    public function testKeepAliveCanBeResetToNull(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setKeepAlive('5m');

        // Act
        $request->setKeepAlive(null);

        // Assert
        $this->assertNull($request->getKeepAlive());
    }

    /**
     * Ensures toArray omits keepAlive when reset to null.
     * Why: null values should not appear in payload.
     */
    public function testToArrayOmitsNullKeepAlive(): void
    {
        // Arrange
        $request = new EmbeddingRequest('nomic-embed-text:latest', 'Hello world');
        $request->setKeepAlive('5m');
        $request->setKeepAlive(null);

        $expected = [
            'model' => 'nomic-embed-text:latest',
            'input' => 'Hello world',
        ];

        // Act
        $result = $request->toArray();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
