# PHP Doctrine module

## Installation
```
composer require scottsmith/doctrine
```

# Laravel Integration
Laravel bootstraps this package.
You need to publish the configuration using `php artisan vendor:publish`.
You can then configure `config/doctrine.php` or use the `.env` file.
 
# Mezzio Integration
Mezzio uses the ConfigProvider in this integration.

You need to publish the configuration using:
```
$ php vendor/scottsmith/doctrine/src/Integration/Laminas/deploy-mezzio-doctrine-config config/autoload
```

You can then configure `config/autoload/doctrine.php` or use the `.env` file.

# PHPUnit
To enable testing with this doctrine module you will need to add this to `phpunit.xml`. This will enable migrations.
```
    <extensions>
        <extension class="ScottSmith\Doctrine\Integration\Testing\MigrationExtension"/>
    </extensions>
```

There are a few helpers to enable getting the DBAL Connection and EntityProviderInterface.
You can create or extend the base TestCase with something like the below.
This example shows the usage of `TestConnectionProviderTrait` in the Laravel integrations so you can use the `DatabaseHelperTrait` on Laravel applications.
```
<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use ScottSmith\Doctrine\Integration\<Laravel/Laminas>\TestConnectionProviderTrait;
use ScottSmith\Doctrine\Integration\Testing\ConnectionAwareInterface;
use ScottSmith\Doctrine\Integration\Testing\EntityManagerAwareInterface;

abstract class TestCase extends BaseTestCase implements ConnectionAwareInterface, EntityManagerAwareInterface 
{
    use TestConnectionProviderTrait;
}
``` 
