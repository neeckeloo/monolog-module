<?php
namespace MonologModuleTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use MonologModule\Exception\RuntimeException;
use MonologModule\Factory\LoggerFactory;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\HandlerPluginManager;
use PHPUnit\Framework\TestCase;

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

    public function testCreateLoggerWithoutName()
    {
        $this->expectException(RuntimeException::class);

        $factory = new LoggerFactory();
        $factory->create([]);
    }

    public function testCreateLoggerWithHandler()
    {
        $factory = new LoggerFactory();
        $factory->setContainer(new ServiceManager());

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
        $factory->setContainer(new ServiceManager());

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

    public function testCreateLoggerWithHandlerWithoutName()
    {
        $this->expectException(RuntimeException::class);

        $factory = new LoggerFactory();
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [],
            ],
        ];
        $factory->create($config);
    }

    public function testCreateLoggerWithHandlerWithInvalidName()
    {
        $this->expectException(RuntimeException::class);

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

        $serviceManager = new ServiceManager();

        $handlerPluginManager = new HandlerPluginManager($serviceManager);
        $formatterPluginManager = new FormatterPluginManager($serviceManager);

        $serviceManager->setService(HandlerPluginManager::class, $handlerPluginManager);
        $serviceManager->setService(FormatterPluginManager::class, $formatterPluginManager);

        $factory->setContainer($serviceManager);

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
        $factory->setContainer(new ServiceManager());

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

    public function testCreateLoggerWithHandlerAndFormatterWithoutName()
    {
        $this->expectException(RuntimeException::class);

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

    public function testCreateLoggerWithHandlerAndFormatterWithInvalidName()
    {
        $this->expectException(RuntimeException::class);

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

    public function testCreateLoggerWithInvalidProcessor()
    {
        $this->expectException(RuntimeException::class);

        $factory = new LoggerFactory();

        $config = [
            'name' => 'foo',
            'processors' => [
                'Monolog\Processor\InvalidProcessor',
            ],
        ];
        $factory->create($config);
    }

    public function testCreateLoggerWithHandlerAndProcessor()
    {
        $factory = new LoggerFactory();
        $factory->setContainer(new ServiceManager());

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => TestHandler::class,
                    'processors' => [
                        TagProcessor::class,
                    ],
                ],
            ],
        ];
        $logger = $factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf(TestHandler::class, $handler);

        $processor = $handler->popProcessor();
        $this->assertInstanceOf(TagProcessor::class, $processor);
    }
}
