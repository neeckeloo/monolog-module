<?php
namespace MonologModuleTest\Factory;

use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use MonologModule\Factory\LoggerFactory;
use PHPUnit_Framework_TestCase;

class LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $this->factory = new LoggerFactory();
    }

    public function testCreateSimpleLogger()
    {
        $logger = $this->factory->create([
            'name' => 'foo',
        ]);
        $this->assertInstanceOf(Logger::class, $logger);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithoutName()
    {
        $this->factory->create([]);
    }

    public function testCreateLoggerWithHandler()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\NullHandler',
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf(Logger::class, $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);
    }

    public function testCreateLoggerWithHandlerIncludingOptions()
    {
        $level = Logger::CRITICAL;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\NullHandler',
                    'options' => [
                        'level' => $level,
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf(Logger::class, $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);

        $this->assertEquals($level, $handler->getLevel());
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerWithoutName()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [],
            ],
        ];
        $this->factory->create($config);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerWithInvalidName()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\InvalidHandler',
                ],
            ],
        ];
        $this->factory->create($config);
    }

    public function testCreateLoggerWithHandlerAndFormatter()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => 'Monolog\Handler\NullHandler',
                    'formatter' => [
                        'name' => JsonFormatter::class,
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf(Logger::class, $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);
    }

    public function testCreateLoggerWithHandlerAndFormatterIncludingOptions()
    {
        $batchMode = JsonFormatter::BATCH_MODE_NEWLINES;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => 'Monolog\Handler\NullHandler',
                    'formatter' => [
                        'name' => JsonFormatter::class,
                        'options' => [
                            'batchMode' => $batchMode,
                        ],
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf(Logger::class, $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);

        $this->assertEquals($batchMode, $formatter->getBatchMode());
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerAndFormatterWithoutName()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'formatter' => [
                        'options' => [
                            'batchMode' => JsonFormatter::BATCH_MODE_JSON,
                        ],
                    ],
                ],
            ],
        ];
        $this->factory->create($config);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerAndFormatterWithInvalidName()
    {
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\InvalidHandler',
                    'formatter' => [
                        'name' => 'Monolog\Formatter\InvalidFormatter',
                    ],
                ],
            ],
        ];
        $this->factory->create($config);
    }

    public function testCreateLoggerWithProcessor()
    {
        $config = [
            'name' => 'foo',
            'processors' => [
                'Monolog\Processor\TagProcessor',
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf(Logger::class, $logger);

        $processor = $logger->popProcessor();
        $this->assertInstanceOf('Monolog\Processor\TagProcessor', $processor);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithInvalidProcessor()
    {
        $config = [
            'name' => 'foo',
            'processors' => [
                'Monolog\Processor\InvalidProcessor',
            ],
        ];
        $this->factory->create($config);
    }
}
