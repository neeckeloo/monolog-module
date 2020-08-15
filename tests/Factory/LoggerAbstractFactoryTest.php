<?php
namespace MonologModuleTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use MonologModule\Factory\LoggerAbstractFactory;
use MonologModule\Factory\LoggerFactory;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LoggerAbstractFactoryTest extends TestCase
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

        $serviceManager = new ServiceManager();

        $serviceManager->setService('Config', ['monolog' => []]);
        $serviceManager->setService(LoggerFactory::class, $loggerFactory);

        $abstractFactory = new LoggerAbstractFactory();
        $instance = $abstractFactory->createServiceWithName($serviceManager, null, 'foo');

        $this->assertInstanceOf('Monolog\Logger', $instance);
    }
}
