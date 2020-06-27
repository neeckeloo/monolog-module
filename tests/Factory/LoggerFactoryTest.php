<?php
namespace MonologModuleTest\Factory;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use MonologModule\Factory\LoggerFactory;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\HandlerPluginManager;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LoggerFactoryTest extends TestCase
{
    public function testCreateSimpleLogger()
    {
        $factory = new LoggerFactory();

        $logger = $factory->create([
            'name' => 'foo',
        ]);

        $this->assertInstanceOf('Monolog\Logger', $logger);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithoutName()
    {
        $factory = new LoggerFactory();
        $factory->create([]);
    }

    public function testCreateLoggerWithHandler()
    {
        $factory = new LoggerFactory();

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $factory->setContainer($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => TestHandler::class,
                ],
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(TestHandler::class, $handler);
    }

    public function testCreateLoggerWithHandlerIncludingOptions()
    {
        $factory = new LoggerFactory();

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $factory->setContainer($serviceLocator);

        $level = Logger::CRITICAL;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => TestHandler::class,
                    'options' => [
                        'level' => $level,
                    ],
                ],
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(TestHandler::class, $handler);

        $this->assertEquals($level, $handler->getLevel());
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerWithoutName()
    {
        $factory = new LoggerFactory();
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [],
            ],
        ];
        $factory->create($config);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerWithInvalidName()
    {
        $factory = new LoggerFactory();
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\InvalidHandler',
                ],
            ],
        ];
        $factory->create($config);
    }

    public function testCreateLoggerWithHandlerAndFormatter()
    {
        $factory = new LoggerFactory();

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->at(0))
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $serviceLocator
            ->expects($this->at(2))
            ->method('has')
            ->with(FormatterPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->expects($this->at(3))
            ->method('get')
            ->with(FormatterPluginManager::class)
            ->will($this->returnValue(null));
        $factory->setContainer($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => TestHandler::class,
                    'formatter' => [
                        'name' => JsonFormatter::class,
                    ],
                ],
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(TestHandler::class, $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);
    }

    public function testCreateLoggerWithHandlerAndFormatterIncludingOptions()
    {
        $factory = new LoggerFactory();

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->at(0))
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $serviceLocator
            ->expects($this->at(2))
            ->method('has')
            ->with(FormatterPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->expects($this->at(3))
            ->method('get')
            ->with(FormatterPluginManager::class)
            ->will($this->returnValue(null));
        $factory->setContainer($serviceLocator);

        $batchMode = JsonFormatter::BATCH_MODE_NEWLINES;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => TestHandler::class,
                    'formatter' => [
                        'name' => JsonFormatter::class,
                        'options' => [
                            'batchMode' => $batchMode,
                        ],
                    ],
                ],
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(TestHandler::class, $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);

        $this->assertEquals($batchMode, $formatter->getBatchMode());
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerAndFormatterWithoutName()
    {
        $factory = new LoggerFactory();

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
        $factory->create($config);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithHandlerAndFormatterWithInvalidName()
    {
        $factory = new LoggerFactory();

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
        $factory->create($config);
    }

    public function testCreateLoggerWithProcessor()
    {
        $factory = new LoggerFactory();

        $config = [
            'name' => 'foo',
            'processors' => [
                TagProcessor::class,
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $processor = $logger->popProcessor();
        $this->assertInstanceOf(TagProcessor::class, $processor);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithInvalidProcessor()
    {
        $factory = new LoggerFactory();

        $config = [
            'name' => 'foo',
            'processors' => [
                'Monolog\Processor\InvalidProcessor',
            ],
        ];
        $factory->create($config);
    }
}
