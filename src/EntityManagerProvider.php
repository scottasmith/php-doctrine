<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use ScottSmith\Doctrine\Configuration\ConfigurationInterface;
use ScottSmith\Doctrine\Configuration\ConfigurationValidator;
use ScottSmith\Doctrine\Exception\ConnectionNotFoundException;

class EntityManagerProvider
{
    /**
     * Cached connections
     *
     * @var array
     */
    private array $connections = [];

    /**
     * @param ConfigurationInterface $configuration
     * @param DependencyResolverInterface|null $resolver = null
     * @throws Exception\ConfigurationException
     */
    public function __construct(
        private ConfigurationInterface $configuration,
        private ?DependencyResolverInterface $resolver = null
    ) {
        ConfigurationValidator::validateConnections($this->configuration->getConfiguration());
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @param string|null $name
     * @return EntityManager
     * @throws ConnectionNotFoundException
     * @throws ORMException
     */
    public function get(string $name = null): EntityManager
    {
        if (null === $name) {
            $name = 'default';
        }

        $configuration = $this->configuration->getConfiguration();

        if (!isset($configuration['connections'][$name])) {
            throw new ConnectionNotFoundException($name);
        }

        $connectionConfig = $configuration['connections'][$name];

        $config = Setup::createAnnotationMetadataConfiguration(
            $connectionConfig['paths'],
            empty($configuration['proxies']),
            empty($configuration['proxies']) ? null : $configuration['proxies'],
            null,
            false
        );

        if (!empty($connectionConfig['cache'])) {
            $config->setQueryCacheImpl($connectionConfig['cache']);
        }

        if ($this->resolver && !empty($connectionConfig['logger'])) {
            $config->setSQLLogger($this->resolver->get($connectionConfig['logger']));
        }

        $entityManager = EntityManager::create(
            [
                'driver' => $connectionConfig['driver'] ?? 'pdo_mysql',
                'dbname' => $connectionConfig['database'] ?? null,
                'user' => $connectionConfig['username'],
                'password' => $connectionConfig['password'],
                'host' => $connectionConfig['host'],
            ],
            $config
        );

        return $this->connections[$name] = $entityManager;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        $configuration = $this->configuration->getConfiguration();
        return isset($configuration['connections'][$name]);
    }
}
