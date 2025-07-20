<?php

declare(strict_types=1);

namespace Tests\Unit\Transport\Domain\Interface;

use PHPUnit\Framework\TestCase;
use Tenqz\Ollama\Transport\Domain\Interface\TransportClientInterface;
use Tenqz\Ollama\Transport\Domain\Interface\ResponseInterface;

/**
 * Test for Transport Client Interface.
 */
class TransportClientInterfaceTest extends TestCase
{
    /**
     * Test that interface exists.
     */
    public function testInterfaceExists(): void
    {
        // Arrange & Act - nothing to arrange or act on for interface existence

        // Assert
        $this->assertTrue(interface_exists(TransportClientInterface::class));
    }

    /**
     * Test that interface has get method.
     */
    public function testInterfaceHasGetMethod(): void
    {
        // Arrange & Act - nothing to arrange or act on for method existence

        // Assert
        $this->assertTrue(method_exists(TransportClientInterface::class, 'get'));
    }

    /**
     * Test that interface has post method.
     */
    public function testInterfaceHasPostMethod(): void
    {
        // Arrange & Act - nothing to arrange or act on for method existence

        // Assert
        $this->assertTrue(method_exists(TransportClientInterface::class, 'post'));
    }

    /**
     * Test that get method has correct endpoint parameter.
     */
    public function testGetMethodHasEndpointParameter(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $getMethod = $reflectionClass->getMethod('get');
        $parameters = $getMethod->getParameters();

        // Assert
        $this->assertEquals('endpoint', $parameters[0]->getName());
    }

    /**
     * Test that get method has optional params parameter.
     */
    public function testGetMethodHasOptionalParamsParameter(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $getMethod = $reflectionClass->getMethod('get');
        $parameters = $getMethod->getParameters();

        // Assert
        $this->assertTrue($parameters[1]->isOptional());
        $this->assertEquals('params', $parameters[1]->getName());
    }

    /**
     * Test that get method returns ResponseInterface.
     */
    public function testGetMethodReturnsResponseInterface(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $getMethod = $reflectionClass->getMethod('get');

        // Assert
        $this->assertEquals(ResponseInterface::class, $getMethod->getReturnType()->getName());
    }

    /**
     * Test that post method has correct endpoint parameter.
     */
    public function testPostMethodHasEndpointParameter(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $postMethod = $reflectionClass->getMethod('post');
        $parameters = $postMethod->getParameters();

        // Assert
        $this->assertEquals('endpoint', $parameters[0]->getName());
    }

    /**
     * Test that post method has optional data parameter.
     */
    public function testPostMethodHasOptionalDataParameter(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $postMethod = $reflectionClass->getMethod('post');
        $parameters = $postMethod->getParameters();

        // Assert
        $this->assertTrue($parameters[1]->isOptional());
        $this->assertEquals('data', $parameters[1]->getName());
    }

    /**
     * Test that post method returns ResponseInterface.
     */
    public function testPostMethodReturnsResponseInterface(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(TransportClientInterface::class);

        // Act
        $postMethod = $reflectionClass->getMethod('post');

        // Assert
        $this->assertEquals(ResponseInterface::class, $postMethod->getReturnType()->getName());
    }
}
