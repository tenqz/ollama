<?php

declare(strict_types=1);

namespace Tests\Unit\Transport\Infrastructure\Http\Response;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;
use Tenqz\Ollama\Transport\Infrastructure\Http\Response\JsonResponse;

/**
 * Tests for JsonResponse.
 */
class JsonResponseTest extends TestCase
{
    /**
     * Ensures JsonResponse implements ResponseInterface.
     * Why: callers type-hint on the response interface.
     */
    public function testImplementsResponseInterface(): void
    {
        // Arrange
        $statusCode = 200;
        $headers = ['Content-Type' => 'application/json'];
        $body = '{"key":"value"}';

        // Act
        $response = new JsonResponse($statusCode, $headers, $body);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Ensures getStatusCode returns provided value.
     * Why: status checks depend on this accessor.
     */
    public function testGetStatusCodeReturnsCorrectValue(): void
    {
        // Arrange
        $statusCode = 200;
        $response = new JsonResponse($statusCode, [], '{}');

        // Act
        $result = $response->getStatusCode();

        // Assert
        $this->assertEquals($statusCode, $result);
    }

    /**
     * Ensures getHeaders returns provided headers.
     * Why: clients may inspect response headers.
     */
    public function testGetHeadersReturnsCorrectValues(): void
    {
        // Arrange
        $headers = ['Content-Type' => 'application/json', 'X-Test' => 'Value'];
        $response = new JsonResponse(200, $headers, '{}');

        // Act
        $result = $response->getHeaders();

        // Assert
        $this->assertEquals($headers, $result);
    }

    /**
     * Ensures getBody returns raw content.
     * Why: clients may need raw unparsed payloads.
     */
    public function testGetBodyReturnsRawContent(): void
    {
        // Arrange
        $body = '{"key":"value","nested":{"foo":"bar"}}';
        $response = new JsonResponse(200, [], $body);

        // Act
        $result = $response->getBody();

        // Assert
        $this->assertEquals($body, $result);
    }

    /**
     * Ensures getData decodes valid JSON body.
     * Why: typical path for JSON APIs.
     */
    public function testGetDataReturnsDecodedJsonData(): void
    {
        // Arrange
        $body = '{"key":"value","nested":{"foo":"bar"}}';
        $response = new JsonResponse(200, [], $body);
        $expectedData = [
            'key'    => 'value',
            'nested' => ['foo' => 'bar'],
        ];

        // Act
        $result = $response->getData();

        // Assert
        $this->assertEquals($expectedData, $result);
    }

    /**
     * Ensures getData throws on invalid JSON body.
     * Why: avoid returning malformed data structures.
     */
    public function testGetDataThrowsExceptionForInvalidJson(): void
    {
        // Arrange
        $body = '{invalid json}';
        $response = new JsonResponse(200, [], $body);

        // Act
        try {
            $response->getData();
            $this->fail('Expected TransportException to be thrown');
        } catch (TransportException $e) {
            // Assert
            $this->assertStringStartsWith('Failed to decode JSON response', $e->getMessage());
        }
    }

    /**
     * Ensures isSuccessful returns true for 2xx statuses.
     * Why: conventional HTTP success range.
     *
     * @dataProvider successfulStatusCodesProvider
     */
    public function testIsSuccessfulReturnsTrueFor2xxStatusCodes(int $statusCode): void
    {
        // Arrange
        $response = new JsonResponse($statusCode, [], '{}');

        // Act
        $result = $response->isSuccessful();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Ensures isSuccessful returns false for non-2xx statuses.
     * Why: consistent failure signaling.
     *
     * @dataProvider nonSuccessfulStatusCodesProvider
     */
    public function testIsSuccessfulReturnsFalseForNon2xxStatusCodes(int $statusCode): void
    {
        // Arrange
        $response = new JsonResponse($statusCode, [], '{}');

        // Act
        $result = $response->isSuccessful();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Data provider for successful status codes.
     *
     * @return array<array<int>>
     */
    public function successfulStatusCodesProvider(): array
    {
        return [
            [200], // OK
            [201], // Created
            [202], // Accepted
            [204], // No Content
            [299], // Custom successful code
        ];
    }

    /**
     * Data provider for non-successful status codes.
     *
     * @return array<array<int>>
     */
    public function nonSuccessfulStatusCodesProvider(): array
    {
        return [
            [100], // Continue
            [199], // Info
            [300], // Multiple Choices
            [301], // Moved Permanently
            [400], // Bad Request
            [404], // Not Found
            [500], // Internal Server Error
            [503], // Service Unavailable
        ];
    }
}
