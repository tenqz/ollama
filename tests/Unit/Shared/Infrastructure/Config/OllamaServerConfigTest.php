<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Infrastructure\Config;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig;

/**
 * @covers \Tenqz\Ollama\Shared\Infrastructure\Config\OllamaServerConfig
 */
class OllamaServerConfigTest extends TestCase
{
    /**
     * Tests that constructor sets default host correctly.
     */
    public function testConstructorSetsDefaultHost(): void
    {
        // Act
        $config = new OllamaServerConfig();

        // Assert
        $this->assertEquals('localhost', $config->getHost());
    }

    /**
     * Tests that constructor sets default port correctly.
     */
    public function testConstructorSetsDefaultPort(): void
    {
        // Act
        $config = new OllamaServerConfig();

        // Assert
        $this->assertEquals(11434, $config->getPort());
    }

    /**
     * Tests that constructor sets custom host correctly.
     */
    public function testConstructorSetsCustomHost(): void
    {
        // Arrange
        $customHost = 'custom-host';

        // Act
        $config = new OllamaServerConfig($customHost);

        // Assert
        $this->assertEquals($customHost, $config->getHost());
    }

    /**
     * Tests that constructor sets custom port correctly.
     */
    public function testConstructorSetsCustomPort(): void
    {
        // Arrange
        $customPort = 12345;

        // Act
        $config = new OllamaServerConfig('localhost', $customPort);

        // Assert
        $this->assertEquals($customPort, $config->getPort());
    }

    /**
     * Tests getHost method returns correct value.
     */
    public function testGetHostReturnsCorrectValue(): void
    {
        // Arrange
        $host = 'test-host';
        $config = new OllamaServerConfig($host);

        // Act
        $result = $config->getHost();

        // Assert
        $this->assertEquals($host, $result);
    }

    /**
     * Tests getPort method returns correct value.
     */
    public function testGetPortReturnsCorrectValue(): void
    {
        // Arrange
        $port = 54321;
        $config = new OllamaServerConfig('localhost', $port);

        // Act
        $result = $config->getPort();

        // Assert
        $this->assertEquals($port, $result);
    }

    /**
     * Tests getBaseUrl method returns correctly formatted URL.
     */
    public function testGetBaseUrlReturnsFormattedUrl(): void
    {
        // Arrange
        $host = 'example-host';
        $port = 8080;
        $config = new OllamaServerConfig($host, $port);
        $expectedUrl = 'http://example-host:8080';

        // Act
        $result = $config->getBaseUrl();

        // Assert
        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Tests getBaseUrl with default values.
     */
    public function testGetBaseUrlWithDefaultValues(): void
    {
        // Arrange
        $config = new OllamaServerConfig();
        $expectedUrl = 'http://localhost:11434';

        // Act
        $result = $config->getBaseUrl();

        // Assert
        $this->assertEquals($expectedUrl, $result);
    }
}
