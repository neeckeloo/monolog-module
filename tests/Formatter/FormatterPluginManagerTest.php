<?php
namespace MonologModuleTest\Formatter;

use MonologModule\Formatter\FormatterPluginManager;
use PHPUnit_Framework_TestCase;
use Stdclass;

class FormatterPluginManagerTest extends PHPUnit_Framework_TestCase
{
    public function testValidatePlugin()
    {
        $formatterPluginManager = new FormatterPluginManager();

        $formatter = $this->getMock('Monolog\Formatter\FormatterInterface');
        $formatterPluginManager->validatePlugin($formatter);
    }

    /**
     * @expectedException MonologModule\Exception\InvalidArgumentException
     */
    public function testValidateInvalidPlugin()
    {
        $formatterPluginManager = new FormatterPluginManager();

        $formatter = new Stdclass;
        $formatterPluginManager->validatePlugin($formatter);
    }
}
