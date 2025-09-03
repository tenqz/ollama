<?php

declare(strict_types=1);

namespace Tests\Unit\Transport\Infrastructure\Http\Client;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Transport\Domain\Exception\TransportException;
use Tenqz\Ollama\Transport\Domain\Response\ResponseInterface;
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
     * Ensures GET requests build correct URL and pass query params to transport.
     * Why: prevents wrong endpoints and missing params in real calls.
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
     * Ensures POST requests send payload to the expected endpoint.
     * Why: validates request body wiring to transport layer.
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
     * Ensures transport exceptions propagate to caller with original message.
     * Why: callers must handle network failures deterministically.
     */
    public function testThrowsExceptionOnTransportError(): void
    {
        // Arrange
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willThrowException(new TransportException('Connection failed'));

        // Act
        try {
            $clientMock->get('/models');
            $this->fail('Expected TransportException to be thrown');
        } catch (TransportException $e) {
            // Assert
            $this->assertSame('Connection failed', $e->getMessage());
        }
    }

    /**
     * Ensures buildUrl handles endpoints with leading slash.
     * Why: avoids double slashes or missing path segments.
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
     * Ensures buildUrl handles endpoints without leading slash.
     * Why: consistent URL building regardless of input format.
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
     * Ensures non-JSON responses are still accessible via getBody().
     * Why: some endpoints may return plain text; body must be readable.
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
     * Ensures getData() throws on non-JSON content.
     * Why: prevents silent decoding errors and enforces response contract.
     */
    public function testGetDataThrowsExceptionForNonJsonResponse(): void
    {
        // Arrange
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willReturn([
                'status'  => 200,
                'headers' => ['Content-Type' => 'text/plain'],
                'body'    => 'Plain text response',
            ]);

        // Act
        $response = $clientMock->get('/text');

        try {
            $response->getData();
            $this->fail('Expected TransportException to be thrown');
        } catch (TransportException $e) {
            // Assert
            $this->assertStringStartsWith('Failed to decode JSON response', $e->getMessage());
        }
    }

    /**
     * Ensures default total timeout is 120 seconds.
     * Why: generation may take long; sane default avoids premature timeouts.
     */
    public function testDefaultTimeoutIs120(): void
    {
        // Arrange
        $client = new CurlTransportClient($this->baseUrl);
        $ref = new \ReflectionClass($client);
        $timeoutProp = $ref->getProperty('timeout');
        $timeoutProp->setAccessible(true);

        // Act
        $value = $timeoutProp->getValue($client);

        // Assert
        $this->assertSame(120, $value);
    }

    /**
     * Ensures default connect timeout is 10 seconds.
     * Why: fail fast on unreachable hosts while allowing long processing.
     */
    public function testDefaultConnectTimeoutIs10(): void
    {
        // Arrange
        $client = new CurlTransportClient($this->baseUrl);
        $ref = new \ReflectionClass($client);
        $connectTimeoutProp = $ref->getProperty('connectTimeout');
        $connectTimeoutProp->setAccessible(true);

        // Act
        $value = $connectTimeoutProp->getValue($client);

        // Assert
        $this->assertSame(10, $value);
    }

    /**
     * Ensures custom total timeout via constructor is respected.
     * Why: callers may need longer/shorter overall timeouts.
     */
    public function testCustomTimeoutIsApplied(): void
    {
        // Arrange
        $client = new CurlTransportClient($this->baseUrl, [], 5, 1);
        $ref = new \ReflectionClass($client);
        $timeoutProp = $ref->getProperty('timeout');
        $timeoutProp->setAccessible(true);

        // Act
        $value = $timeoutProp->getValue($client);

        // Assert
        $this->assertSame(5, $value);
    }

    /**
     * Ensures custom connect timeout via constructor is respected.
     * Why: callers can tune connection establishment separately.
     */
    public function testCustomConnectTimeoutIsApplied(): void
    {
        // Arrange
        $client = new CurlTransportClient($this->baseUrl, [], 5, 1);
        $ref = new \ReflectionClass($client);
        $connectTimeoutProp = $ref->getProperty('connectTimeout');
        $connectTimeoutProp->setAccessible(true);

        // Act
        $value = $connectTimeoutProp->getValue($client);

        // Assert
        $this->assertSame(1, $value);
    }

    /**
     * Ensures cURL timeout error is propagated as TransportException with message.
     * Why: callers must distinguish timeout from other failures.
     */
    public function testPostPropagatesTimeoutException(): void
    {
        // Arrange
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        $clientMock->expects($this->once())
            ->method('executeCurlRequest')
            ->willThrowException(new TransportException('cURL error: Operation timed out', 28));

        // Act
        try {
            $clientMock->post('/timeout', []);
            $this->fail('Expected TransportException to be thrown');
        } catch (TransportException $e) {
            // Assert
            $this->assertSame('cURL error: Operation timed out', $e->getMessage());
        }
    }
}
