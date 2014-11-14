<?php
namespace MonologModule\Handler;

use MonologModule\Exception;
use Monolog\Handler\HandlerInterface;
use Zend\ServiceManager\AbstractPluginManager;

class HandlerPluginManager extends AbstractPluginManager
{
    /**
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof HandlerInterface) {
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement Monolog\Handler\HandlerInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
