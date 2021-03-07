<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\Migrations\Configuration\Configuration as MigrationConfiguration;
use ScottSmith\Doctrine\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Helper\HelperSet;

class ConfigurationFactory
{
    /**
     * Disable instantiation
     */
    private function __construct()
    {
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param string|null $connectionName
     * @return HelperSet
     * @throws Exception\ConfigurationException
     * @throws Exception\ConnectionNotFoundException
     * @throws ORMException
     */
    public static function forEntityManager(
        ConfigurationInterface $configuration,
        ?string $connectionName = null
    ): HelperSet {
        $name = $connectionName ?? self::getConnectionName($configuration);

        $provider = new EntityManagerProvider($configuration);
        $entityManager = $provider->get($name);

        return new HelperSet(
            [
                'em' => new EntityManagerHelper($entityManager),
                'db' => new ConnectionHelper($entityManager->getConnection(), $name)
            ]
        );
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param string|null $connectionName
     * @return DependencyFactory
     * @throws Exception\ConfigurationException
     * @throws Exception\ConnectionNotFoundException
     * @throws ORMException
     */
    public static function forMigrations(ConfigurationInterface $configuration, ?string $connectionName = null)
    {
        $connectionName = $connectionName ?? self::getConnectionName($configuration);

        $managers = new EntityManagerProvider($configuration);
        $entityManager = $managers->get($connectionName);

        $migrationConfig = new MigrationConfiguration();
        $migrationConfig->setAllOrNothing(true);
        $migrationConfig->setCheckDatabasePlatform(true);

        $additionalMigrationPaths = $configuration->getConfiguration()
            ['connections'][$connectionName]['migration_paths'] ?? [];

        foreach ($additionalMigrationPaths as $namespace => $migrationPath) {
            $migrationConfig->addMigrationsDirectory($namespace, $migrationPath);
        }

        $metaStorageConfig = new TableMetadataStorageConfiguration();
        $metaStorageConfig->setTableName('doctrine_migrations');

        $migrationConfig->setMetadataStorageConfiguration($metaStorageConfig);

        return DependencyFactory::fromEntityManager(
            new ExistingConfiguration($migrationConfig),
            new ExistingEntityManager($entityManager)
        );
    }

    /**
     * @param ConfigurationInterface $configuration
     * @return string
     */
    private static function getConnectionName(ConfigurationInterface $configuration): string
    {
        static $name = null;

        /**
         * Cache result as we won't always have the --connection argument
         */
        if ($name) {
            return $name;
        }

        /**
         * Search $argv for --connection
         */
        foreach ($_SERVER['argv'] as $idx => $arg) {
            if (strpos($arg, '--connection') !== false) {
                [$key, $value] = explode('=', $arg);

                if (is_string($value)) {
                    $name = $value;
                }

                unset($_SERVER['argv'][$idx]);
            }
        }

        // In-case found the --connection we must to re-index the array
        $_SERVER['argv'] = array_values($_SERVER['argv']);

        return $name = $name ?? $configuration->getDefault();
    }
}
