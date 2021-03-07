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
     * ConnectionInterface configuration
     *
     * @var array
     */
    private array $configuration;

    /**
     * Cached connections
     *
     * @var array
     */
    private array $connections = [];

    /**
     * @var DependencyResolverInterface|null
     */
    private ?DependencyResolverInterface $resolver;

    /**
     * @param ConfigurationInterface $configuration
     * @param DependencyResolverInterface|null $resolver = null
     * @throws Exception\ConfigurationException
     */
    public function __construct(ConfigurationInterface $configuration, ?DependencyResolverInterface $resolver = null)
    {
        $this->configuration = $configuration->getConfiguration();
        $this->resolver = $resolver;

        ConfigurationValidator::validateConnections($this->configuration);
    }

    /**
     * @throws ConnectionNotFoundException
     * @throws ORMException
     */
    public function registerAll(): array
    {
        foreach ($this->configuration['connections'] as $name => $config) {
            $this->get($name);
        }
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

        if (!isset($this->configuration['connections'][$name])) {
            throw new ConnectionNotFoundException($name);
        }

        $connectionConfig = $this->configuration['connections'][$name];

        $config = Setup::createAnnotationMetadataConfiguration(
            $connectionConfig['paths'],
            empty($this->configuration['proxies']),
            empty($this->configuration['proxies']) ? null : $this->configuration['proxies'],
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
        return isset($this->configuration['connections'][$name]);
    }
}
