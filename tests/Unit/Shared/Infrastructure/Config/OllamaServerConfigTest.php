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
     * Ensures default host is 'localhost' when not provided.
     * Why: predictable defaults simplify configuration.
     */
    public function testConstructorSetsDefaultHost(): void
    {
        // Act
        $config = new OllamaServerConfig();

        // Assert
        $this->assertEquals('localhost', $config->getHost());
    }

    /**
     * Ensures default port is 11434 when not provided.
     * Why: matches Ollama's default port for local server.
     */
    public function testConstructorSetsDefaultPort(): void
    {
        // Act
        $config = new OllamaServerConfig();

        // Assert
        $this->assertEquals(11434, $config->getPort());
    }

    /**
     * Ensures custom host passed to constructor is applied.
     * Why: allows targeting non-default hosts.
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
     * Ensures custom port passed to constructor is applied.
     * Why: allows targeting non-default ports.
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
     * Ensures getHost returns configured host.
     * Why: downstream consumers rely on this value.
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
     * Ensures getPort returns configured port.
     * Why: downstream consumers rely on this value.
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
     * Ensures getBaseUrl formats URL as http://host:port.
     * Why: used to construct transport base URL.
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
     * Ensures getBaseUrl uses defaults when none provided.
     * Why: predictable base URL without explicit config.
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
