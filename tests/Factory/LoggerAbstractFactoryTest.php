<?php
namespace MonologModuleTest\Factory;

use Monolog\Logger;
use MonologModule\Factory\LoggerAbstractFactory;
use PHPUnit_Framework_TestCase;

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
            'loggers' => [
                'foo' => [
                    'name' => 'Monolog\Handler\NullHandler',
                ],
                'bar' => [],
            ],
        ];

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
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

        $loggerFactory = $this->getMock('MonologModule\Factory\LoggerFactory');
        $loggerFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($logger));

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('Config')
            ->will($this->returnValue([]));

        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with('MonologModule\Factory\LoggerFactory')
            ->will($this->returnValue($loggerFactory));

        $abstractFactory = new LoggerAbstractFactory();
        $instance = $abstractFactory->createServiceWithName($serviceLocator, null, 'foo');
        $this->assertInstanceOf('Monolog\Logger', $instance);
    }
}
