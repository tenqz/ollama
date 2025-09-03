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
    /** @var GenerationResponse */
    private $response;

    /**
     * Prepare fresh DTO per test to avoid state leakage.
     */
    protected function setUp(): void
    {
        $this->response = new GenerationResponse('The sky is blue because it is the color of the sky.');
    }

    /**
     * Symmetric cleanup.
     */
    protected function tearDown(): void
    {
        $this->response = null;
    }

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
        // Act
        $result = $this->response->getModel();

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
        // Act
        $result = $this->response->getCreatedAt();

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
        $model = 'llama3.2';

        // Act
        $this->response->setModel($model);
        $result = $this->response->getModel();

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
        // Act
        $result = $this->response->setModel('model');

        // Assert
        $this->assertSame($this->response, $result);
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
        $createdAt = '2023-08-04T19:22:45.499127Z';

        // Act
        $this->response->setCreatedAt($createdAt);
        $result = $this->response->getCreatedAt();

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
        // Act
        $result = $this->response->setCreatedAt('2023-08-04T19:22:45.499127Z');

        // Assert
        $this->assertSame($this->response, $result);
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
        $this->response->setModel('llama3.2');

        // Act
        $result = $this->response->toArray();

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
        $this->response->setModel($model);

        // Act
        $result = $this->response->toArray();

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
        $this->response->setCreatedAt('2023-08-04T19:22:45.499127Z');

        // Act
        $result = $this->response->toArray();

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
        $this->response->setCreatedAt($createdAt);

        // Act
        $result = $this->response->toArray();

        // Assert
        $this->assertSame($createdAt, $result['created_at']);
    }

    /**
     * Ensures done can be set and retrieved.
     * Why: non-stream mode returns final done=true.
     *
     * @test
     */
    public function testItShouldSetAndGetDone(): void
    {
        // Arrange
        $this->response->setDone(true);

        // Assert
        $this->assertTrue($this->response->getDone());
    }

    /**
     * Ensures toArray includes done when set.
     * Why: payload must expose completion flag.
     *
     * @test
     */
    public function testItShouldSerializeDoneToArray(): void
    {
        // Arrange
        $this->response->setDone(true);

        // Act
        $array = $this->response->toArray();

        // Assert
        $this->assertTrue($array['done']);
    }

    /**
     * Ensures numeric metrics can be set and retrieved.
     * Why: final response includes performance metrics.
     *
     * @test
     */

    /**
     * Ensures each metric setter persists value retrievable via getter.
     * Why: validate numeric metric fields individually.
     *
     * @dataProvider providerMetricsSetGet
     */
    public function testMetricSetterStoresValue(string $setter, $value, string $getter): void
    {
        // Act
        $this->response->{$setter}($value);
        $result = $this->response->{$getter}();
        // Assert
        $this->assertSame($value, $result);
    }

    /**
     * Metric setters/getters mapping with sample values.
     *
     * @return array<int, array{0:string,1:mixed,2:string}>
     */
    public function providerMetricsSetGet(): array
    {
        return [
            ['setTotalDuration', 10706818083, 'getTotalDuration'],
            ['setLoadDuration', 6338219291, 'getLoadDuration'],
            ['setPromptEvalCount', 26, 'getPromptEvalCount'],
            ['setPromptEvalDuration', 130079000, 'getPromptEvalDuration'],
            ['setEvalCount', 259, 'getEvalCount'],
            ['setEvalDuration', 4232710000, 'getEvalDuration'],
        ];
    }

    /**
     * Ensures context tokens can be set and retrieved.
     * Why: context enables conversational memory across requests.
     *
     * @test
     */
    public function testItShouldSetAndGetContext(): void
    {
        // Arrange
        $this->response->setContext([1, 2, 3]);

        // Assert
        $this->assertSame([1, 2, 3], $this->response->getContext());
    }

    /**
     * Ensures toArray includes metrics and context when set.
     * Why: final response must serialize extended metadata.
     *
     * @test
     */

    /**
     * Ensures single metric serializes to correct key in array.
     * Why: validate array mapping per metric.
     *
     * @dataProvider providerMetricsArrayMapping
     */
    public function testSerializeSingleMetricToArray(string $setter, $value, string $key): void
    {
        // Act
        $this->response->{$setter}($value);
        $array = $this->response->toArray();
        // Assert
        $this->assertSame($value, $array[$key]);
    }

    /**
     * Metric field to array-key mapping with sample values.
     *
     * @return array<int, array{0:string,1:mixed,2:string}>
     */
    public function providerMetricsArrayMapping(): array
    {
        return [
            ['setTotalDuration', 10706818083, 'total_duration'],
            ['setLoadDuration', 6338219291, 'load_duration'],
            ['setPromptEvalCount', 26, 'prompt_eval_count'],
            ['setPromptEvalDuration', 130079000, 'prompt_eval_duration'],
            ['setEvalCount', 259, 'eval_count'],
            ['setEvalDuration', 4232710000, 'eval_duration'],
        ];
    }

    /**
     * Ensures context serializes to array when set.
     * Why: enables conversational memory handoff.
     *
     * @test
     */
    public function testSerializeContextToArray(): void
    {
        // Arrange
        $this->response->setContext([1, 2, 3]);
        // Act
        $array = $this->response->toArray();
        // Assert
        $this->assertSame([1, 2, 3], $array['context']);
    }

    /**
     * Ensures complete non-stream response serializes exactly as the API example.
     * Why: end-to-end payload mapping using one assertion.
     *
     * @test
     */
    public function testItShouldSerializeCompleteNonStreamPayload(): void
    {
        // Arrange
        $this->response = new GenerationResponse('The sky is blue because it is the color of the sky.');
        $this->response
            ->setModel('llama3.2')
            ->setCreatedAt('2023-08-04T19:22:45.499127Z')
            ->setDone(true)
            ->setContext([1, 2, 3])
            ->setTotalDuration(5043500667)
            ->setLoadDuration(5025959)
            ->setPromptEvalCount(26)
            ->setPromptEvalDuration(325953000)
            ->setEvalCount(290)
            ->setEvalDuration(4709213000);

        $expected = [
            'response'             => 'The sky is blue because it is the color of the sky.',
            'model'                => 'llama3.2',
            'created_at'           => '2023-08-04T19:22:45.499127Z',
            'done'                 => true,
            'context'              => [1, 2, 3],
            'total_duration'       => 5043500667,
            'load_duration'        => 5025959,
            'prompt_eval_count'    => 26,
            'prompt_eval_duration' => 325953000,
            'eval_count'           => 290,
            'eval_duration'        => 4709213000,
        ];

        // Act
        $array = $this->response->toArray();

        // Assert
        $this->assertSame($expected, $array);
    }
}
