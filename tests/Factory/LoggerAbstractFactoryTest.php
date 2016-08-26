<?php
namespace MonologModuleTest\Factory;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use MonologModule\Factory\LoggerAbstractFactory;
use MonologModule\Factory\LoggerFactory;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerAbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    public function canCreateServiceWithNameProvider()
    {
        return [
            ['foo', true],
            ['bar', false],
            ['baz', false],
        ];
    }

    /**
     * @dataProvider canCreateServiceWithNameProvider
     */
    public function testCanCreateServiceWithName($name, $expected)
    {
        $config = [
            'monolog' => [
                'loggers' => [
                    'foo' => [
                        'name' => NullHandler::class,
                    ],
                    'bar' => [],
                ],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $abstractFactory = new LoggerAbstractFactory();
        $output = $abstractFactory->canCreateServiceWithName($serviceLocator, null, $name);
        $this->assertSame($expected, $output);
    }

    public function testCreateServiceWithName()
    {
        $logger = new Logger('foo');

        $loggerFactory = $this->createMock(LoggerFactory::class);
        $loggerFactory
            ->method('create')
            ->will($this->returnValue($logger));

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('Config')
            ->will($this->returnValue(['monolog' => []]));

        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with(LoggerFactory::class)
            ->will($this->returnValue($loggerFactory));

        $abstractFactory = new LoggerAbstractFactory();
        $instance = $abstractFactory->createServiceWithName($serviceLocator, null, 'foo');
        $this->assertInstanceOf('Monolog\Logger', $instance);
    }
}
