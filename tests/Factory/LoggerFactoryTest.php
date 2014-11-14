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
        $this->assertInstanceOf('Monolog\Logger', $logger);
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
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('MonologModule\Handler\HandlerPluginManager')
            ->will($this->returnValue(null));
        $this->factory->setServiceLocator($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name' => 'Monolog\Handler\NullHandler',
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);
    }

    public function testCreateLoggerWithHandlerIncludingOptions()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('MonologModule\Handler\HandlerPluginManager')
            ->will($this->returnValue(null));
        $this->factory->setServiceLocator($serviceLocator);

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
        $this->assertInstanceOf('Monolog\Logger', $logger);

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
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('MonologModule\Handler\HandlerPluginManager')
            ->will($this->returnValue(null));
        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with('MonologModule\Formatter\FormatterPluginManager')
            ->will($this->returnValue(null));
        $this->factory->setServiceLocator($serviceLocator);

        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => 'Monolog\Handler\NullHandler',
                    'formatter' => [
                        'name' => 'Monolog\Formatter\JsonFormatter',
                    ],
                ],
            ],
        ];
        $logger = $this->factory->create($config);
        $this->assertInstanceOf('Monolog\Logger', $logger);

        $handler = $logger->popHandler();
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf('Monolog\Formatter\JsonFormatter', $formatter);
    }

    public function testCreateLoggerWithHandlerAndFormatterIncludingOptions()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('MonologModule\Handler\HandlerPluginManager')
            ->will($this->returnValue(null));
        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with('MonologModule\Formatter\FormatterPluginManager')
            ->will($this->returnValue(null));
        $this->factory->setServiceLocator($serviceLocator);

        $batchMode = JsonFormatter::BATCH_MODE_NEWLINES;
        $config = [
            'name' => 'foo',
            'handlers' => [
                'default' => [
                    'name'      => 'Monolog\Handler\NullHandler',
                    'formatter' => [
                        'name' => 'Monolog\Formatter\JsonFormatter',
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
        $this->assertInstanceOf('Monolog\Handler\NullHandler', $handler);

        $formatter = $handler->getFormatter();
        $this->assertInstanceOf('Monolog\Formatter\JsonFormatter', $formatter);

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
        $this->assertInstanceOf('Monolog\Logger', $logger);

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
