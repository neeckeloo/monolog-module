<?php
namespace MonologModule\Handler;

use MonologModule\Exception;
use Monolog\Handler\HandlerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

class HandlerPluginManager extends AbstractPluginManager
{
    protected $instanceOf = HandlerInterface::class;

    /**
     * @param  mixed $instance
     * @throws InvalidServiceException
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }

    /**
     * @param  mixed $instance
     * @throws Exception\InvalidArgumentException
     */
    public function validatePlugin($instance)
    {
        $this->validate($instance);
    }
}
