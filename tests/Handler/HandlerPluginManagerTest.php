<?php
namespace MonologModuleTest\Handler;

use MonologModule\Handler\HandlerPluginManager;
use PHPUnit_Framework_TestCase;
use Stdclass;

class HandlerPluginManagerTest extends PHPUnit_Framework_TestCase
{
    public function testValidatePlugin()
    {
        $handlerPluginManager = new HandlerPluginManager();

        $handler = $this->getMock('Monolog\Handler\HandlerInterface');
        $handlerPluginManager->validatePlugin($handler);
    }

    /**
     * @expectedException MonologModule\Exception\InvalidArgumentException
     */
    public function testValidateInvalidPlugin()
    {
        $handlerPluginManager = new HandlerPluginManager();

        $handler = new Stdclass;
        $handlerPluginManager->validatePlugin($handler);
    }
}
