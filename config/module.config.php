<?php
use Monolog\Handler\GelfHandler;
use MonologModule\Factory\FormatterPluginManagerFactory;
use MonologModule\Factory\HandlerPluginManagerFactory;
use MonologModule\Factory\LoggerAbstractFactory;
use MonologModule\Factory\LoggerFactory;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\Factory\GelfHandlerFactory;
use MonologModule\Handler\HandlerPluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            FormatterPluginManager::class => FormatterPluginManagerFactory::class,
            HandlerPluginManager::class => HandlerPluginManagerFactory::class,
            LoggerFactory::class => InvokableFactory::class,
        ],
        'abstract_factories' => [
            LoggerAbstractFactory::class,
        ],
    ],

    'monolog' => [
        'handler_plugin_manager' => [
            'factories' => [
                GelfHandler::class => GelfHandlerFactory::class,
            ],
        ],
        'formatter_plugin_manager' => [],
    ],
];
