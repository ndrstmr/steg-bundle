<?php

declare(strict_types=1);

namespace Steg\Bundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Steg\Bundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    private Processor $processor;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function testMinimalDsnConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'default' => ['dsn' => 'mock://default'],
            ],
        ]]);

        self::assertSame('mock://default', $config['connections']['default']['dsn']);
        self::assertSame(120, $config['connections']['default']['timeout']);
        self::assertSame('EMPTY', $config['connections']['default']['api_key']);
        self::assertNull($config['default_connection']);
    }

    public function testBaseUrlConfig(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'vllm' => [
                    'base_url' => 'http://localhost:8000/v1',
                    'model' => 'llama-3.3-70b-awq',
                    'timeout' => 60,
                ],
            ],
        ]]);

        self::assertSame('http://localhost:8000/v1', $config['connections']['vllm']['base_url']);
        self::assertSame('llama-3.3-70b-awq', $config['connections']['vllm']['model']);
        self::assertSame(60, $config['connections']['vllm']['timeout']);
    }

    public function testMultipleConnectionsWithDefaultConnection(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'vllm_local' => ['dsn' => 'vllm://localhost:8000/v1?model=llama'],
                'mock' => ['dsn' => 'mock://default'],
            ],
            'default_connection' => 'vllm_local',
        ]]);

        self::assertSame('vllm_local', $config['default_connection']);
        self::assertCount(2, $config['connections']);
    }

    public function testTimeoutMustBeGreaterThanZero(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'default' => ['dsn' => 'mock://default', 'timeout' => 0],
            ],
        ]]);
    }

    public function testMissingDsnAndBaseUrlThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('"dsn" or "base_url"');

        $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'broken' => ['model' => 'some-model'],
            ],
        ]]);
    }

    public function testBaseUrlWithoutModelThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('"model"');

        $this->processor->processConfiguration($this->configuration, [[
            'connections' => [
                'broken' => ['base_url' => 'http://localhost:8000/v1'],
            ],
        ]]);
    }

    public function testEmptyConnectionsThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration($this->configuration, [[
            'connections' => [],
        ]]);
    }
}
