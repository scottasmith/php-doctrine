<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas\Container;

use RuntimeException;
use ScottSmith\Doctrine\Integration\Laminas\Configuration;
use ScottSmith\Doctrine\Integration\Laminas\DependencyResolver;

class EntityManagerProviderFactory
{
    /**
     * @throws RuntimeException
     */
    public function __invoke(): EntityManagerProvider
    {
        if (!file_exists(implode('/', 'config', 'autoload', 'doctrine.php'))) {
            throw new RuntimeException('doctrine.php does not exist in ./config/autoload');
        }

        $configuration = new Configuration();
        $dependencyResolver = new DependencyResolver($this->app);

        return new EntityManagerProvider($configuration, $dependencyResolver);
    }
}