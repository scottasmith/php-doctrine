<?php

use ScottSmith\Doctrine\ConfigurationFactory;
use ScottSmith\Doctrine\Integration\Laravel\Configuration;

require_once "vendor/autoload.php";

return $_SERVER['argv'][0] === 'vendor/bin/doctrine-migrations' ?
    Configuration::forMigrations(new Configuration()):
    Configuration::forEntityManager(new Configuration());
