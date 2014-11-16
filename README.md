MonologModule
=============

Monolog integration into Zend Framework 2

[![Build Status](https://img.shields.io/travis/neeckeloo/MonologModule.svg?style=flat)](http://travis-ci.org/neeckeloo/MonologModule)
[![Latest Stable Version](http://img.shields.io/packagist/v/neeckeloo/monolog-module.svg?style=flat)](https://packagist.org/packages/neeckeloo/monolog-module)
[![Total Downloads](http://img.shields.io/packagist/dt/neeckeloo/monolog-module.svg?style=flat)](https://packagist.org/packages/neeckeloo/monolog-module)
[![Coverage Status](http://img.shields.io/coveralls/neeckeloo/MonologModule.svg?style=flat)](https://coveralls.io/r/neeckeloo/MonologModule)
[![Dependencies Status](https://www.versioneye.com/user/projects/5465c709f8a4ae1c9900010d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5465c709f8a4ae1c9900010d)

Requirements
------------

* PHP 5.4 or higher
* [Monolog 1.11 or higher](http://www.github.com/Seldaek/monolog)
* [Zend Framework 2.3 or higher](http://www.github.com/zendframework/zf2)

Installation
------------

MonologModule must be installed through Composer. For Composer documentation, please refer to [getcomposer.org](http://getcomposer.org).

You can install the module from command line:
```sh
$ php composer.phar require neeckeloo/monolog-module:~0.1
```

Alternatively, you can also add manually the dependency in your `composer.json` file:
```json
{
    "require": {
        "neeckeloo/monolog-module": "~0.1"
    }
}
```

Enable the module by adding `MonologModule` key in your `application.config.php` file.

Usage
-----

### Configuring a logger

This is the configuration of a logger that can be retrieved with key ```Log\App``` in the service manager. A channel name ```default``` is also defined to identify to which part of the application a record is related.

```php
return [
    'monolog' => [
        'loggers' => [
            'Log\App' => [
                'name' => 'default',
            ],
        ],
    ],
];
```

### Adding a handler

The logger itself does not know how to handle a record. It delegates it to some handlers. The code above registers two handlers in the stack to allow handling records in two different ways.

```php
return [
    'monolog' => [
        'loggers' => [
            'Log\App' => [
                'name' => 'default',
                'handlers' => [
                    'stream' => [
                        'name' => 'Monolog\Handler\StreamHandler',
                        'options' => [
                            'path'   => 'data/log/application.log',
                            'level'  => \Monolog\Logger::DEBUG,
                        ],
                    ],
                    'fire_php' => [
                        'name' => 'Monolog\Handler\FirePHPHandler',
                    ],
                ],
            ],
        ],
    ],
];
```

### Using processors

If you want to add extra information (tags, user IP, ...) to the records before they are handled, you should add some processors. The code above adds two processors that add an unique identifier and the current request URI, request method and client IP to a log record.

```php
return [
    'monolog' => [
        'loggers' => [
            'Log\App' => [
                'name' => 'default',
                'handlers' => [
                    'default' => [
                        'name' => 'Monolog\Handler\StreamHandler',
                        'options' => [
                            'path'   => 'data/log/application.log',
                            'level'  => \Monolog\Logger::DEBUG,
                        ],
                    ],
                ],
                'processors' => [
                    'Monolog\Processor\UidProcessor',
                    'Monolog\Processor\WebProcessor',
                ],
            ],
        ],
    ],
];
```

### Retrieving a logger

Once the configuration is complete, you can retrieve an instance of the logger as below:

```php
$logger = $serviceManager->get('Log\App');
$logger->debug('debug message');
```
