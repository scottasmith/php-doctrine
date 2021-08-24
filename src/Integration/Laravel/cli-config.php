<?php

use ScottSmith\Doctrine\ConfigurationFactory;
use ScottSmith\Doctrine\Integration\Laravel\Configuration;

require_once "vendor/autoload.php";

return $_SERVER['argv'][0] === 'vendor/bin/doctrine-migrations' ?
    ConfigurationFactory::forMigrations(new Configuration()):
        ConfigurationFactory::forEntityManager(new Configuration());
