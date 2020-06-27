<?php
namespace MonologModuleTest\Formatter;

use Monolog\Formatter\FormatterInterface;
use MonologModule\Formatter\FormatterPluginManager;
use PHPUnit\Framework\TestCase;
use stdClass;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerTest extends TestCase
{
    public function testRetrievePlugin()
    {
        $formatter = $this->createMock(FormatterInterface::class);

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $formatterPluginManager = new FormatterPluginManager($serviceLocator, [
            'services' => [
                FormatterInterface::class => $formatter,
            ],
        ]);

        $formatterReturned = $formatterPluginManager->get(FormatterInterface::class);
        $this->assertSame($formatter, $formatterReturned);
    }

    public function testValidateInvalidPlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $formatterPluginManager = new FormatterPluginManager($serviceLocator);

        $this->expectException(InvalidServiceException::class);

        $formatter = new stdClass;
        $formatterPluginManager->validatePlugin($formatter);
    }
}
