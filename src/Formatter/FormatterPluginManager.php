<?php
namespace MonologModule\Formatter;

use MonologModule\Exception;
use Monolog\Formatter\FormatterInterface;
use Zend\ServiceManager\AbstractPluginManager;

class FormatterPluginManager extends AbstractPluginManager
{
    /**
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof FormatterInterface) {
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement Monolog\Formatter\FormatterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
