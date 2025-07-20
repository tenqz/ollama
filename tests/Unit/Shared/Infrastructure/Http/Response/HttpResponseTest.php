<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Infrastructure\Http\Response;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Shared\Infrastructure\Http\Response\HttpResponse;

/**
 * Test for HttpResponse.
 */
class HttpResponseTest extends TestCase
{
    /**
     * Test that status code is correctly returned.
     */
    public function testGetStatusCodeReturnsCorrectValue(): void
    {
        // Arrange
        $statusCode = 200;
        $response = new HttpResponse($statusCode, [], '');

        // Act
        $result = $response->getStatusCode();

        // Assert
        $this->assertEquals($statusCode, $result);
    }

    /**
     * Test that headers are correctly returned.
     */
    public function testGetHeadersReturnsCorrectValues(): void
    {
        // Arrange
        $headers = ['Content-Type' => 'application/json'];
        $response = new HttpResponse(200, $headers, '');

        // Act
        $result = $response->getHeaders();

        // Assert
        $this->assertEquals($headers, $result);
    }

    /**
     * Test that body is correctly returned.
     */
    public function testGetBodyReturnsCorrectValue(): void
    {
        // Arrange
        $body = '{"key": "value"}';
        $response = new HttpResponse(200, [], $body);

        // Act
        $result = $response->getBody();

        // Assert
        $this->assertEquals($body, $result);
    }

    /**
     * Test isSuccessful method with successful status codes.
     *
     * @dataProvider successfulStatusCodesProvider
     */
    public function testIsSuccessfulWithSuccessfulStatusCodes(int $statusCode): void
    {
        // Arrange
        $response = new HttpResponse($statusCode, [], '');

        // Act
        $result = $response->isSuccessful();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test isSuccessful method with non-successful status codes.
     *
     * @dataProvider nonSuccessfulStatusCodesProvider
     */
    public function testIsSuccessfulWithNonSuccessfulStatusCodes(int $statusCode): void
    {
        // Arrange
        $response = new HttpResponse($statusCode, [], '');

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
            [203], // Non-Authoritative Information
            [204], // No Content
            [205], // Reset Content
            [206], // Partial Content
            [207], // Multi-Status
            [208], // Already Reported
            [226], // IM Used
            [299], // Edge case - still successful
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
            [199], // Edge case - informational
            [300], // Multiple Choices
            [301], // Moved Permanently
            [302], // Found
            [399], // Edge case - redirection
            [400], // Bad Request
            [401], // Unauthorized
            [404], // Not Found
            [499], // Edge case - client error
            [500], // Internal Server Error
            [501], // Not Implemented
            [502], // Bad Gateway
            [599], // Edge case - server error
        ];
    }

    /**
     * Test that the headers array is not modified after response creation.
     */
    public function testHeadersAreImmutable(): void
    {
        // Arrange
        $headers = ['Content-Type' => 'application/json'];
        $response = new HttpResponse(200, $headers, '');

        // Act
        $headers['New-Header'] = 'new value';
        $result = $response->getHeaders();

        // Assert
        $this->assertArrayNotHasKey('New-Header', $result);
    }
}
