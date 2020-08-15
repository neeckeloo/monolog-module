<?php
namespace MonologModuleTest\Factory;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use MonologModule\Factory\LoggerAbstractFactory;
use MonologModule\Factory\LoggerFactory;
use PHPUnit\Framework\TestCase;

class LoggerAbstractFactoryTest extends TestCase
{
    public function canCreateServiceProvider()
    {
        return [
            ['foo', true],
            ['bar', false],
            ['baz', false],
        ];
    }

    /**
     * @dataProvider canCreateServiceProvider
     */
    public function testCanCreateService($name, $expected)
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
        $output = $abstractFactory->canCreate($serviceLocator, $name);

        $this->assertSame($expected, $output);
    }

    public function testCreateService()
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
        $instance = ($abstractFactory)($serviceManager, 'foo');

        $this->assertInstanceOf('Monolog\Logger', $instance);
    }
}
