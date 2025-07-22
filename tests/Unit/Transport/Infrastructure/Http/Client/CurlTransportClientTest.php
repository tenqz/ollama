<?php

declare(strict_types=1);

namespace Tests\Unit\Transport\Infrastructure\Http\Client;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Interfaces\ResponseInterface;
use Tenqz\Ollama\Transport\Infrastructure\Http\Client\CurlTransportClient;

/**
 * Tests for CurlTransportClient.
 */
class CurlTransportClientTest extends TestCase
{
    /** @var CurlTransportClient */
    private $client;

    /** @var string */
    private $baseUrl;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->baseUrl = 'http://api.example.com';
        $this->client = new CurlTransportClient($this->baseUrl);
    }

    /**
     * Test that client correctly constructs URLs for GET requests.
     */
    public function testGetRequestConstructsCorrectUrl(): void
    {
        // Create a mock for CurlTransportClient, overriding only executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Configure the mock to check URL and parameters
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->with(
                $this->equalTo($this->baseUrl . '/models'),
                $this->equalTo([]),
                $this->equalTo('GET'),
                $this->equalTo(['param1' => 'value1', 'param2' => 'value2'])
            )
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => '{"success":true}',
            ]);

        // Act
        $response = $clientMock->get('/models', ['param1' => 'value1', 'param2' => 'value2']);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Test that client correctly constructs POST requests.
     */
    public function testPostRequestSendsCorrectData(): void
    {
        // Create a mock for CurlTransportClient, overriding only executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Prepare test data
        $requestData = [
            'model'  => 'llama2',
            'prompt' => 'Hello, world!',
        ];

        // Configure the mock to check URL and parameters
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->with(
                $this->equalTo($this->baseUrl . '/generate'),
                $this->equalTo($requestData),
                $this->equalTo('POST'),
                $this->equalTo([])
            )
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => '{"response":"Hello there!"}',
            ]);

        // Act
        $response = $clientMock->post('/generate', $requestData);

        // Assert
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Test that client handles transport errors.
     */
    public function testThrowsExceptionOnTransportError(): void
    {
        // Create a mock for CurlTransportClient, overriding only executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Configure the mock to simulate curl error
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willThrowException(new TransportException('Connection failed'));

        // Assert
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Connection failed');

        // Act
        $clientMock->get('/models');
    }

    /**
     * Test that client normalizes URLs with leading slash.
     */
    public function testNormalizesUrlWithLeadingSlash(): void
    {
        // Arrange
        $baseUrlWithTrailingSlash = $this->baseUrl . '/';
        $endpoint = '/endpoint';
        $expectedUrl = $this->baseUrl . '/endpoint';

        // Create mock for TransportClient to check if executeCurlRequest is called with correct URL
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$baseUrlWithTrailingSlash])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Expect executeCurlRequest to be called with proper URL
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->with(
                $this->equalTo($expectedUrl),
                $this->anything(),
                $this->anything(),
                $this->anything()
            )
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => '{"success":true}',
            ]);

        // Act
        $clientMock->get($endpoint);

        // Assert is handled by the mock expectations
    }

    /**
     * Test that client normalizes URLs without leading slash.
     */
    public function testNormalizesUrlWithoutLeadingSlash(): void
    {
        // Arrange
        $baseUrlWithTrailingSlash = $this->baseUrl . '/';
        $endpoint = 'endpoint'; // No leading slash
        $expectedUrl = $this->baseUrl . '/endpoint';

        // Create mock for TransportClient to check if executeCurlRequest is called with correct URL
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$baseUrlWithTrailingSlash])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Expect executeCurlRequest to be called with proper URL
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->with(
                $this->equalTo($expectedUrl),
                $this->anything(),
                $this->anything(),
                $this->anything()
            )
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => '{"success":true}',
            ]);

        // Act
        $clientMock->get($endpoint);

        // Assert is handled by the mock expectations
    }

    /**
     * Test that client handles non-JSON responses for body retrieval.
     */
    public function testHandlesNonJsonResponses(): void
    {
        // Create a mock for CurlTransportClient, overriding only executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Configure the mock to return non-JSON response
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'text/plain'],
                'body'    => 'Plain text response',
            ]);

        // Act
        $response = $clientMock->get('/text');

        // Assert - verify access to raw body content only
        $this->assertEquals('Plain text response', $response->getBody());
    }

    /**
     * Test that getData() throws exception for non-JSON response.
     */
    public function testGetDataThrowsExceptionForNonJsonResponse(): void
    {
        // Create a mock for CurlTransportClient, overriding only executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Configure the mock to return non-JSON response
        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'text/plain'],
                'body'    => 'Plain text response',
            ]);

        // Act
        $response = $clientMock->get('/text');

        // Assert
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');
        $response->getData(); // Should throw exception for invalid JSON
    }
}
