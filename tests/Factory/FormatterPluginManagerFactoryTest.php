<?php
namespace MonologModuleTest\Factory;

use MonologModule\Factory\FormatterPluginManagerFactory;
use PHPUnit_Framework_TestCase;

class FormatterPluginManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveFormatterInstance()
    {
        $config = [
            'monolog' => [
                'formatter_plugin_manager' => [
                    'services' => [
                        'foo' => $this->getMock('Monolog\Formatter\FormatterInterface')
                    ],
                ],
            ],
        ];

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $formatterPluginManagerFactory = new FormatterPluginManagerFactory();

        $formatterPluginManager = $formatterPluginManagerFactory->createService($serviceLocator);
        $this->assertInstanceOf('MonologModule\Formatter\FormatterPluginManager', $formatterPluginManager);

        // Assert that a ServiceLocatorInterface service has been injected.
        // If it would be injected via the deprecated ServiceLocatorAwareInitializer, a E_USER_DEPRECATED would have been triggered.
        $this->assertInstanceOf('Zend\ServiceManager\ServiceLocatorInterface', $formatterPluginManager->getServiceLocator());

        $service = $formatterPluginManager->get('foo');
        $this->assertInstanceOf('Monolog\Formatter\FormatterInterface', $service);
    }
}
