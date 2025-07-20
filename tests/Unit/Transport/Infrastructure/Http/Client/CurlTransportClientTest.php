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
        // Создаем мок для CurlTransportClient, подменяя только executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Настраиваем мок, чтобы проверить URL и параметры
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
        // Создаем мок для CurlTransportClient, подменяя только executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Подготавливаем тестовые данные
        $requestData = [
            'model'  => 'llama2',
            'prompt' => 'Hello, world!',
        ];

        // Настраиваем мок, чтобы проверить URL и параметры
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
        // Создаем мок для CurlTransportClient, подменяя только executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Настраиваем мок для имитации ошибки curl
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
     * Test that client normalizes URLs properly.
     */
    public function testNormalizesUrlCorrectly(): void
    {
        // Arrange: create client with trailing slash in base URL
        $clientWithTrailingSlash = new CurlTransportClient($this->baseUrl . '/');

        // Используем рефлексию для доступа к защищенным методам
        $reflectionClass = new \ReflectionClass(CurlTransportClient::class);
        $method = $reflectionClass->getMethod('buildUrl');
        $method->setAccessible(true);

        // Act
        $urlWithLeadingSlash = $method->invoke($clientWithTrailingSlash, '/endpoint');
        $urlWithoutLeadingSlash = $method->invoke($clientWithTrailingSlash, 'endpoint');

        // Assert
        $this->assertEquals($this->baseUrl . '/endpoint', $urlWithLeadingSlash);
        $this->assertEquals($this->baseUrl . '/endpoint', $urlWithoutLeadingSlash);
    }

    /**
     * Test that client handles non-JSON responses.
     */
    public function testHandlesNonJsonResponses(): void
    {
        // Создаем мок для CurlTransportClient, подменяя только executeCurlRequest
        $clientMock = $this->getMockBuilder(CurlTransportClient::class)
            ->setConstructorArgs([$this->baseUrl])
            ->onlyMethods(['executeCurlRequest'])
            ->getMock();

        // Настраиваем мок для возврата не-JSON ответа
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
        $this->assertEquals('Plain text response', $response->getBody());

        // При вызове getData() должен быть создан массив с ключом data
        $data = $response->getData();
        $this->assertArrayHasKey('data', $data);
    }
}
