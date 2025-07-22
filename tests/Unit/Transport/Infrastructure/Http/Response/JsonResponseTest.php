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
     * Test that JsonResponse implements ResponseInterface.
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
     * Test getStatusCode returns correct status code.
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
     * Test getHeaders returns correct headers.
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
     * Test getBody returns raw body content.
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
     * Test getData returns decoded JSON data.
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
     * Test getData throws exception for invalid JSON.
     */
    public function testGetDataThrowsExceptionForInvalidJson(): void
    {
        // Arrange
        $body = '{invalid json}';
        $response = new JsonResponse(200, [], $body);

        // Act & Assert
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');

        $response->getData();
    }

    /**
     * Test isSuccessful returns true for 2xx status codes.
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
     * Test isSuccessful returns false for non-2xx status codes.
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
