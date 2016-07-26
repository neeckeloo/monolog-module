<?php
namespace MonologModuleTest\Formatter;

use Monolog\Formatter\FormatterInterface;
use MonologModule\Formatter\FormatterPluginManager;
use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerTest extends PHPUnit_Framework_TestCase
{
    public function testValidatePlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new FormatterPluginManager($serviceLocator);

        $handler = $this->createMock(FormatterInterface::class);
        $handlerPluginManager->validatePlugin($handler);
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\InvalidServiceException
     */
    public function testValidateInvalidPlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new FormatterPluginManager($serviceLocator);

        $handler = new stdClass;
        $handlerPluginManager->validatePlugin($handler);
    }
}
