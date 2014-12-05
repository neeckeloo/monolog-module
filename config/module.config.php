<?php
return [
    'service_manager' => [
        'invokables' => [
            'MonologModule\Factory\LoggerFactory' => 'MonologModule\Factory\LoggerFactory',
        ],
        'factories' => [
            'MonologModule\Formatter\FormatterPluginManager' => 'MonologModule\Factory\FormatterPluginManagerFactory',
            'MonologModule\Handler\HandlerPluginManager'     => 'MonologModule\Factory\HandlerPluginManagerFactory',
        ],
        'abstract_factories' => [
            'MonologModule\Factory\LoggerAbstractFactory',
        ],
    ],

    'monolog' => [
        'handler_plugin_manager' => [
            'factories' => [
                'Monolog\Handler\GelfHandler' => 'MonologModule\Handler\Factory\GelfHandlerFactory',
            ],
        ],
        'formatter_plugin_manager' => [],
    ],
];
