Monolog module for Laminas
=================================

Module to integrate Monolog with Laminas projects.

[![Build Status](https://img.shields.io/travis/neeckeloo/monolog-module.svg?style=flat-square)](http://travis-ci.org/neeckeloo/monolog-module)
[![Latest Stable Version](http://img.shields.io/packagist/v/neeckeloo/monolog-module.svg?style=flat-square)](https://packagist.org/packages/neeckeloo/monolog-module)
[![Total Downloads](http://img.shields.io/packagist/dt/neeckeloo/monolog-module.svg?style=flat-square)](https://packagist.org/packages/neeckeloo/monolog-module)
[![Coverage Status](http://img.shields.io/coveralls/neeckeloo/MonologModule.svg?style=flat-square)](https://coveralls.io/r/neeckeloo/MonologModule)

## Requirements

* PHP 7.2+
* [monolog/monolog ^1.24 || ^2.0](http://www.github.com/Seldaek/monolog)
* [laminas/laminas-servicemanager ^3.3.2](https://github.com/laminas/laminas-servicemanager)

## Installation

MonologModule must be installed through Composer. For Composer documentation, please refer to [getcomposer.org](http://getcomposer.org).

You can install the module from command line:

```sh
$ composer require neeckeloo/monolog-module
```

Enable the module by adding `MonologModule` key in your `application.config.php` file.

## Usage

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
                        'name' => StreamHandler::class,
                        'options' => [
                            'path'   => 'data/log/application.log',
                            'level'  => Logger::DEBUG,
                        ],
                    ],
                    'fire_php' => [
                        'name' => FirePHPHandler::class,
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
                        'name' => StreamHandler::class,
                        'options' => [
                            'path'   => 'data/log/application.log',
                            'level'  => Logger::DEBUG,
                        ],
                    ],
                ],
                'processors' => [
                    UidProcessor::class,
                    WebProcessor::class,
                ],
            ],
        ],
    ],
];
```

You can also add processors to a specific handler.

```php
return [
    'monolog' => [
        'loggers' => [
            'Log\App' => [
                'name' => 'default',
                'handlers' => [
                    'default' => [
                        'name' => StreamHandler::class,
                        'options' => [
                            'path'   => 'data/log/application.log',
                            'level'  => Logger::DEBUG,
                        ],
                        'processors' => [
                            UidProcessor::class,
                            WebProcessor::class,
                        ],
                    ],
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

## Testing

``` bash
$ vendor/bin/phpunit
```

## Credits

- [Nicolas Eeckeloo](https://github.com/neeckeloo)
- [All Contributors](https://github.com/RiskioFr/monolog-module/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/RiskioFr/monolog-module/blob/master/LICENSE) for more information.
