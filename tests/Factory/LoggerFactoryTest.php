<?php
namespace MonologModuleTest\Factory;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use MonologModule\Factory\LoggerFactory;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\HandlerPluginManager;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $this->assertInstanceOf('Monolog\Logger', $logger);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testCreateLoggerWithoutName()
    {
        $this->factory->create([]);
    }

    public function testCreateLoggerWithHandler()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $this->factory->setContainer($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => NullHandler::class,
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(NullHandler::class, $handler);
    }

    public function testCreateLoggerWithHandlerIncludingOptions()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('has')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(true));
        $serviceLocator
            ->method('get')
            ->with(HandlerPluginManager::class)
            ->will($this->returnValue(null));
        $this->factory->setContainer($serviceLocator);

        $level = Logger::CRITICAL;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => NullHandler::class,
                    'options' => [
                        'level' => $level,
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(NullHandler::class, $handler);

        $this->assertEquals($level, $handler->getLevel());
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
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
     * @expectedException \MonologModule\Exception\RuntimeException
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
        $this->factory->setContainer($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => NullHandler::class,
                    'formatter' => [
                        'name' => JsonFormatter::class,
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(NullHandler::class, $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);
    }

    public function testCreateLoggerWithHandlerAndFormatterIncludingOptions()
    {
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
        $this->factory->setContainer($serviceLocator);

        $batchMode = JsonFormatter::BATCH_MODE_NEWLINES;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => NullHandler::class,
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
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(NullHandler::class, $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf(JsonFormatter::class, $formatter);

        $this->assertEquals($batchMode, $formatter->getBatchMode());
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
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
     * @expectedException \MonologModule\Exception\RuntimeException
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
                TagProcessor::class,
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $processor = $logger->popProcessor();
        $this->assertInstanceOf(TagProcessor::class, $processor);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
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
