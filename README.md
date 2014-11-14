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

Configuration
-------------

```php
return [
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
];
```

Usage
-----

```php
$logger = $serviceManager->get('Log\App');
$logger->debug('debug message');
```
