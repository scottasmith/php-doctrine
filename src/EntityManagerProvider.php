<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use ScottSmith\Doctrine\Caching\DoctrineCachingProviderInterface;
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
     * @var bool
     */
    static private bool $typesInitialised = false;

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
        // Do we have a cache version?
        if ($this->connections[$name]) {
            return $this->connections[$name];
        }

        if (null === $name) {
            $name = 'default';
        }

        $configuration = $this->configuration->getConfiguration();

        if (!self::$typesInitialised && is_array($configuration['types'])) {
            $this->registerTypes($configuration['types']);
            self::$typesInitialised = true;
        }

        if (!isset($configuration['connections'][$name])) {
            throw new ConnectionNotFoundException($name);
        }

        $connectionConfig = $configuration['connections'][$name];

        $cache = $configuration['cache'] instanceof DoctrineCachingProviderInterface
            ? $configuration['cache']->getCachingProvider()
            : $configuration['cache'];

        $config = Setup::createConfiguration(
            empty($configuration['proxies']),
            empty($configuration['proxies']) ? null : $configuration['proxies'],
            $cache ?? null
        );

        $config->setMetadataDriverImpl(new AttributeDriver($connectionConfig['paths']));

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
            ] + $connectionConfig,
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

    private function registerTypes(array $types)
    {
        foreach ($types as $typeName => $class) {
            if (is_string($typeName) && is_string($class)) {
                Type::addType($typeName, $class);
            }
        }
    }
}
