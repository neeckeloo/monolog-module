<?php
return [
    'service_manager' => [
        'invokables' => [
            'MonologModule\Factory\LoggerFactory' => 'MonologModule\Factory\LoggerFactory',
        ],
        'factories' => [
            'MonologModule\Formatter\FormatterPluginManager' => 'MonologModule\Factory\FormatterluginManagerFactory',
            'MonologModule\Handler\HandlerPluginManager'     => 'MonologModule\Factory\HandlerPluginManagerFactory',
        ],
        'abstract_factories' => [
            'MonologModule\Factory\LoggerAbstractFactory',
        ],
    ],

    'monolog' => [
        'handler_plugin_manager' => [],
        'formatter_plugin_manager' => [],
    ],
];
