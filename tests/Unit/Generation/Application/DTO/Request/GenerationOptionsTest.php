<?php

declare(strict_types=1);

namespace Tests\Unit\Generation\Application\DTO\Request;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Generation\Application\DTO\Request\GenerationOptions;

/**
 * Tests for GenerationOptions DTO.
 */
class GenerationOptionsTest extends TestCase
{
    /** @var GenerationOptions */
    private $options;

    /**
     * Prepare a fresh DTO for each test to avoid state leakage.
     */
    protected function setUp(): void
    {
        $this->options = new GenerationOptions();
    }

    /**
     * Clean up after test run (symmetry with setUp).
     */
    protected function tearDown(): void
    {
        $this->options = null;
    }

    /**
     * Ensures default values are null for documented getters.
     * Why: unset options must be omitted from payloads.
     *
     * @dataProvider providerDefaultsNull
     */
    public function testDefaultIsNull(string $getter): void
    {
        // Act
        $value = $this->options->{$getter}();
        // Assert
        $this->assertNull($value);
    }

    /**
     * Getters that should return null by default.
     *
     * @return array<int, array{0:string}>
     */
    public function providerDefaultsNull(): array
    {
        return [
            ['getNumCtx'],
            ['getRepeatLastN'],
            ['getRepeatPenalty'],
            ['getTemperature'],
            ['getSeed'],
            ['getStop'],
            ['getNumPredict'],
            ['getTopK'],
            ['getTopP'],
            ['getMinP'],
        ];
    }

    /**
     * Ensures setters return self to enable fluent API.
     * Why: supports concise options configuration.
     *
     * @dataProvider providerSetterReturnsSelf
     */
    public function testSetterReturnsSelf(string $setter, $value): void
    {
        // Act
        $result = $this->options->{$setter}($value);
        // Assert
        $this->assertSame($this->options, $result);
    }

    /**
     * Setters and sample values that should return self.
     *
     * @return array<int, array{0:string,1:mixed}>
     */
    public function providerSetterReturnsSelf(): array
    {
        return [
            ['setNumCtx', 1024],
            ['setRepeatLastN', 33],
            ['setRepeatPenalty', 1.2],
            ['setTemperature', 0.8],
            ['setSeed', 42],
            ['setStop', ["\n", 'user:']],
            ['setNumPredict', 100],
            ['setTopK', 20],
            ['setTopP', 0.9],
            ['setMinP', 0.05],
        ];
    }

    /**
     * Ensures each setter stores its value retrievable via getter.
     * Why: state integrity.
     *
     * @dataProvider providerSetterStoresValue
     */
    public function testSetterStoresValue(string $setter, $value, string $getter): void
    {
        // Act
        $this->options->{$setter}($value);
        $result = $this->options->{$getter}();
        // Assert
        $this->assertEquals($value, $result);
    }

    /**
     * Setter→getter round-trip values.
     *
     * @return array<int, array{0:string,1:mixed,2:string}>
     */
    public function providerSetterStoresValue(): array
    {
        return [
            ['setNumCtx', 1024, 'getNumCtx'],
            ['setRepeatLastN', 33, 'getRepeatLastN'],
            ['setRepeatPenalty', 1.2, 'getRepeatPenalty'],
            ['setTemperature', 0.8, 'getTemperature'],
            ['setSeed', 42, 'getSeed'],
            ['setStop', ["\n", 'user:'], 'getStop'],
            ['setNumPredict', 100, 'getNumPredict'],
            ['setTopK', 20, 'getTopK'],
            ['setTopP', 0.9, 'getTopP'],
            ['setMinP', 0.05, 'getMinP'],
        ];
    }

    /**
     * Ensures toArray emits empty payload when nothing is set.
     * Why: avoid sending unnecessary keys.
     */
    public function testToArrayEmptyWhenNoOptionsSet(): void
    {
        // Act
        $result = $this->options->toArray();
        // Assert
        $this->assertSame([], $result);
    }

    /**
     * Ensures toArray emits only a single key when one option is set.
     * Why: minimal payload per option.
     *
     * @dataProvider providerToArraySingleOption
     */
    public function testToArraySingleOption(string $setter, $value, string $key, $expected): void
    {
        // Act
        $this->options->{$setter}($value);
        $result = $this->options->toArray();
        // Assert
        $this->assertEquals([$key => $expected], $result);
    }

    /**
     * Data provider for single-option toArray emission.
     *
     * @return array<int, array{0:string,1:mixed,2:string,3:mixed}>
     */
    public function providerToArraySingleOption(): array
    {
        return [
            ['setNumCtx', 1024, 'num_ctx', 1024],
            ['setRepeatLastN', 33, 'repeat_last_n', 33],
            ['setRepeatPenalty', 1.2, 'repeat_penalty', 1.2],
            ['setTemperature', 0.8, 'temperature', 0.8],
            ['setSeed', 42, 'seed', 42],
            ['setStop', ["\n", 'user:'], 'stop', ["\n", 'user:']],
            ['setNumPredict', 100, 'num_predict', 100],
            ['setTopK', 20, 'top_k', 20],
            ['setTopP', 0.9, 'top_p', 0.9],
            ['setMinP', 0.05, 'min_p', 0.05],
        ];
    }

    /**
     * Ensures toArray omits stop when it's an empty array.
     * Why: API expects no 'stop' key if empty.
     */
    public function testToArrayOmitsStopWhenEmpty(): void
    {
        // Arrange
        $this->options->setStop([]);
        // Act
        $result = $this->options->toArray();
        // Assert
        $this->assertSame([], $result);
    }

    /**
     * Ensures toArray includes all fields when every option is set.
     * Why: serialization should cover all documented options.
     */
    public function testToArrayIncludesAllFieldsWhenSet(): void
    {
        // Arrange
        $this->options
            ->setSeed(42)
            ->setNumPredict(100)
            ->setTopK(20)
            ->setTopP(0.9)
            ->setMinP(0.0)
            ->setRepeatLastN(33)
            ->setTemperature(0.8)
            ->setRepeatPenalty(1.2)
            ->setStop(["\n", 'user:'])
            ->setNumCtx(1024);

        $expected = [
            'seed'           => 42,
            'num_predict'    => 100,
            'top_k'          => 20,
            'top_p'          => 0.9,
            'min_p'          => 0.0,
            'repeat_last_n'  => 33,
            'temperature'    => 0.8,
            'repeat_penalty' => 1.2,
            'stop'           => ["\n", 'user:'],
            'num_ctx'        => 1024,
        ];

        // Act
        $result = $this->options->toArray();
        // Assert
        $this->assertEquals($expected, $result);
    }
}
