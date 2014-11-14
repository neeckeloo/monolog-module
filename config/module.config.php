<?php
return [
    'service_manager' => [
        'invokables' => [
            'MonologModule\Factory\LoggerFactory' => 'MonologModule\Factory\LoggerFactory',
        ],
        'abstract_factories' => [
            'MonologModule\Factory\LoggerAbstractFactory',
        ],
    ],
];
