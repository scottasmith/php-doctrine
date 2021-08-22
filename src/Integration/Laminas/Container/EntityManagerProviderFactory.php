<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas\Container;

use Psr\Container\ContainerInterface;
use RuntimeException;
use ScottSmith\Doctrine\Integration\Laminas\Configuration;
use ScottSmith\Doctrine\Integration\Laminas\DependencyResolver;
use ScottSmith\Doctrine\EntityManagerProvider;

class EntityManagerProviderFactory
{
    /**
     * @param ContainerInterface $container
     * @throws RuntimeException
     */
    public function __invoke(ContainerInterface $container): EntityManagerProvider
    {
        $config = $container->get('config');
        if (!isset($config['doctrine'])) {
            throw new RuntimeException('doctrine.php does not exist in ./config/autoload');
        }

        $configuration = new Configuration($config['doctrine']);
        $dependencyResolver = new DependencyResolver($container);

        return new EntityManagerProvider($configuration, $dependencyResolver);
    }
}