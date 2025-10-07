<?php

declare(strict_types=1);

namespace Tests\Unit\Embedding\Application\DTO\Response;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Embedding\Application\DTO\Response\EmbeddingResponse;
use Tenqz\Ollama\Embedding\Domain\Exception\EmbeddingException;

/**
 * @covers \Tenqz\Ollama\Embedding\Application\DTO\Response\EmbeddingResponse
 */
class EmbeddingResponseTest extends TestCase
{
    /** @var EmbeddingResponse */
    private $response;

    /** @var array<int, float[]> */
    private $sampleEmbeddings;

    /**
     * Prepare fresh DTO per test to avoid state leakage.
     */
    protected function setUp(): void
    {
        // Create sample 768-dimensional embedding vector
        $this->sampleEmbeddings = [array_fill(0, 768, 0.1)];
        $this->response = new EmbeddingResponse($this->sampleEmbeddings);
    }

    /**
     * Symmetric cleanup.
     */
    protected function tearDown(): void
    {
        $this->response = null;
        $this->sampleEmbeddings = null;
    }

    /**
     * Ensures constructor stores the embeddings array.
     * Why: embeddings are the primary output of generation.
     *
     * @test
     */
    public function testItShouldCreateResponseWithEmbeddings(): void
    {
        // Arrange
        $embeddings = [array_fill(0, 768, 0.1)];

        // Act
        $response = new EmbeddingResponse($embeddings);

        // Assert
        $this->assertSame($embeddings, $response->getEmbeddings());
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
     * Ensures default totalDuration is null.
     * Why: metrics may be absent in minimal responses.
     *
     * @test
     */
    public function testItShouldHaveNullTotalDurationByDefault(): void
    {
        // Act
        $result = $this->response->getTotalDuration();

        // Assert
        $this->assertNull($result);
    }

    /**
     * Ensures default loadDuration is null.
     * Why: metrics may be absent in minimal responses.
     *
     * @test
     */
    public function testItShouldHaveNullLoadDurationByDefault(): void
    {
        // Act
        $result = $this->response->getLoadDuration();

        // Assert
        $this->assertNull($result);
    }

    /**
     * Ensures default promptEvalCount is null.
     * Why: metrics may be absent in minimal responses.
     *
     * @test
     */
    public function testItShouldHaveNullPromptEvalCountByDefault(): void
    {
        // Act
        $result = $this->response->getPromptEvalCount();

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
        $model = 'nomic-embed-text:latest';

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
     * Ensures totalDuration can be assigned and retrieved.
     * Why: performance metrics may be needed by clients.
     *
     * @test
     */
    public function testItShouldSetAndGetTotalDuration(): void
    {
        // Arrange
        $duration = 145991785;

        // Act
        $this->response->setTotalDuration($duration);
        $result = $this->response->getTotalDuration();

        // Assert
        $this->assertSame($duration, $result);
    }

    /**
     * Ensures setTotalDuration returns self for method chaining.
     * Why: fluent setup for DTOs.
     *
     * @test
     */
    public function testItShouldReturnSelfFromSetTotalDuration(): void
    {
        // Act
        $result = $this->response->setTotalDuration(1000);

        // Assert
        $this->assertSame($this->response, $result);
    }

    /**
     * Ensures loadDuration can be assigned and retrieved.
     * Why: performance metrics may be needed by clients.
     *
     * @test
     */
    public function testItShouldSetAndGetLoadDuration(): void
    {
        // Arrange
        $duration = 5659447;

        // Act
        $this->response->setLoadDuration($duration);
        $result = $this->response->getLoadDuration();

        // Assert
        $this->assertSame($duration, $result);
    }

    /**
     * Ensures setLoadDuration returns self for method chaining.
     * Why: fluent setup for DTOs.
     *
     * @test
     */
    public function testItShouldReturnSelfFromSetLoadDuration(): void
    {
        // Act
        $result = $this->response->setLoadDuration(1000);

        // Assert
        $this->assertSame($this->response, $result);
    }

    /**
     * Ensures promptEvalCount can be assigned and retrieved.
     * Why: token count metrics may be needed by clients.
     *
     * @test
     */
    public function testItShouldSetAndGetPromptEvalCount(): void
    {
        // Arrange
        $count = 9;

        // Act
        $this->response->setPromptEvalCount($count);
        $result = $this->response->getPromptEvalCount();

        // Assert
        $this->assertSame($count, $result);
    }

    /**
     * Ensures setPromptEvalCount returns self for method chaining.
     * Why: fluent setup for DTOs.
     *
     * @test
     */
    public function testItShouldReturnSelfFromSetPromptEvalCount(): void
    {
        // Act
        $result = $this->response->setPromptEvalCount(9);

        // Assert
        $this->assertSame($this->response, $result);
    }

    /**
     * Ensures getEmbeddings returns all embedding vectors.
     * Why: batch processing support.
     *
     * @test
     */
    public function testItShouldReturnAllEmbeddingsViaGetter(): void
    {
        // Act
        $result = $this->response->getEmbeddings();

        // Assert
        $this->assertSame($this->sampleEmbeddings, $result);
    }

    /**
     * Ensures getEmbedding returns first embedding vector.
     * Why: single text processing convenience method.
     *
     * @test
     */
    public function testItShouldReturnFirstEmbeddingViaSingularGetter(): void
    {
        // Act
        $result = $this->response->getEmbedding();

        // Assert
        $this->assertSame($this->sampleEmbeddings[0], $result);
    }

    /**
     * Ensures getEmbedding throws exception when embeddings is empty.
     * Why: prevent undefined behavior on empty results.
     *
     * @test
     */
    public function testItShouldThrowExceptionWhenGettingEmbeddingFromEmptyArray(): void
    {
        // Arrange
        $response = new EmbeddingResponse([]);

        // Assert & Act
        $this->expectException(EmbeddingException::class);
        $response->getEmbedding();
    }

    /**
     * Ensures exception message is clear when getting embedding from empty array.
     * Why: helpful error messages for debugging.
     *
     * @test
     */
    public function testItShouldHaveClearExceptionMessageForEmptyEmbeddings(): void
    {
        // Arrange
        $response = new EmbeddingResponse([]);

        // Assert & Act
        $this->expectExceptionMessage('No embeddings available in response');
        $response->getEmbedding();
    }

    /**
     * Ensures getDimension returns correct vector dimension.
     * Why: clients need to know embedding size.
     *
     * @test
     */
    public function testItShouldReturnCorrectDimension(): void
    {
        // Act
        $result = $this->response->getDimension();

        // Assert
        $this->assertEquals(768, $result);
    }

    /**
     * Ensures getDimension returns 0 for empty embeddings.
     * Why: edge case handling for empty results.
     *
     * @test
     */
    public function testItShouldReturnZeroDimensionForEmptyEmbeddings(): void
    {
        // Arrange
        $response = new EmbeddingResponse([]);

        // Act
        $result = $response->getDimension();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * Ensures getDimension returns 0 when first embedding is empty.
     * Why: edge case handling for malformed data.
     *
     * @test
     */
    public function testItShouldReturnZeroDimensionWhenFirstEmbeddingIsEmpty(): void
    {
        // Arrange
        $response = new EmbeddingResponse([[]]);

        // Act
        $result = $response->getDimension();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * Ensures getCount returns number of embedding vectors.
     * Why: batch processing count validation.
     *
     * @test
     */
    public function testItShouldReturnCorrectCount(): void
    {
        // Act
        $result = $this->response->getCount();

        // Assert
        $this->assertEquals(1, $result);
    }

    /**
     * Ensures getCount returns 0 for empty embeddings.
     * Why: edge case handling for empty results.
     *
     * @test
     */
    public function testItShouldReturnZeroCountForEmptyEmbeddings(): void
    {
        // Arrange
        $response = new EmbeddingResponse([]);

        // Act
        $result = $response->getCount();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * Ensures getCount returns correct value for batch processing.
     * Why: validates count for multiple embeddings.
     *
     * @test
     */
    public function testItShouldReturnCorrectCountForBatchProcessing(): void
    {
        // Arrange
        $embeddings = [
            array_fill(0, 768, 0.1),
            array_fill(0, 768, 0.2),
            array_fill(0, 768, 0.3),
        ];
        $response = new EmbeddingResponse($embeddings);

        // Act
        $result = $response->getCount();

        // Assert
        $this->assertEquals(3, $result);
    }

    /**
     * Ensures toArray emits only embeddings when metadata is absent.
     * Why: avoid extra fields in minimal payloads.
     *
     * @test
     */
    public function testItShouldConvertToArrayWithOnlyEmbeddings(): void
    {
        // Act
        $result = $this->response->toArray();

        // Assert
        $this->assertSame(['embeddings' => $this->sampleEmbeddings], $result);
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
        $this->response->setModel('nomic-embed-text:latest');

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
        $model = 'nomic-embed-text:latest';
        $this->response->setModel($model);

        // Act
        $result = $this->response->toArray();

        // Assert
        $this->assertSame($model, $result['model']);
    }

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
            ['setTotalDuration', 145991785, 'getTotalDuration'],
            ['setLoadDuration', 5659447, 'getLoadDuration'],
            ['setPromptEvalCount', 9, 'getPromptEvalCount'],
        ];
    }

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
            ['setTotalDuration', 145991785, 'total_duration'],
            ['setLoadDuration', 5659447, 'load_duration'],
            ['setPromptEvalCount', 9, 'prompt_eval_count'],
        ];
    }

    /**
     * Ensures complete response serializes exactly as the API example.
     * Why: end-to-end payload mapping using one assertion.
     *
     * @test
     */
    public function testItShouldSerializeCompletePayload(): void
    {
        // Arrange
        $embeddings = [array_fill(0, 768, 0.1)];
        $this->response = new EmbeddingResponse($embeddings);
        $this->response
            ->setModel('nomic-embed-text:latest')
            ->setTotalDuration(145991785)
            ->setLoadDuration(5659447)
            ->setPromptEvalCount(9);

        $expected = [
            'embeddings'        => $embeddings,
            'model'             => 'nomic-embed-text:latest',
            'total_duration'    => 145991785,
            'load_duration'     => 5659447,
            'prompt_eval_count' => 9,
        ];

        // Act
        $array = $this->response->toArray();

        // Assert
        $this->assertSame($expected, $array);
    }

    /**
     * Ensures batch embeddings are serialized correctly.
     * Why: validates batch processing serialization.
     *
     * @test
     */
    public function testItShouldSerializeBatchEmbeddings(): void
    {
        // Arrange
        $embeddings = [
            array_fill(0, 768, 0.1),
            array_fill(0, 768, 0.2),
        ];
        $response = new EmbeddingResponse($embeddings);

        $expected = [
            'embeddings' => $embeddings,
        ];

        // Act
        $array = $response->toArray();

        // Assert
        $this->assertSame($expected, $array);
    }

    /**
     * Ensures empty embeddings array is serialized correctly.
     * Why: edge case handling for empty results.
     *
     * @test
     */
    public function testItShouldSerializeEmptyEmbeddings(): void
    {
        // Arrange
        $response = new EmbeddingResponse([]);

        $expected = [
            'embeddings' => [],
        ];

        // Act
        $array = $response->toArray();

        // Assert
        $this->assertSame($expected, $array);
    }
}
