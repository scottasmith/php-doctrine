<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Testing;

use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\ORMException;
use Exception;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\AfterLastTestHook;
use ScottSmith\Doctrine\Configuration\ConfigurationInterface;
use ScottSmith\Doctrine\ConfigurationFactory;
use ScottSmith\Doctrine\EntityManagerProvider;
use ScottSmith\Doctrine\Exception\ConfigurationException;
use ScottSmith\Doctrine\Exception\ConnectionNotFoundException;
use ScottSmith\Doctrine\Integration\Laravel\Configuration;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class MigrationExtension implements BeforeFirstTestHook, AfterLastTestHook
{
    /**
     * @var string
     */
    private string $connectionName;

    /**
     * @var ConfigurationInterface
     */
    private ConfigurationInterface $configuration;

    /**
     * @throws ORMException
     * @throws ConfigurationException
     * @throws ConnectionNotFoundException
     * @throws Exception
     */
    public function executeBeforeFirstTest(): void
    {
        $this->connectionName = $this->getDbConnection();
        $this->configuration = new Configuration();
        $managers = new EntityManagerProvider($this->configuration);
        $entityManager = $managers->get($this->connectionName);

        $connection = $entityManager->getConnection();

        $databaseName = $connection->quoteIdentifier($connection->getDatabase());
        $connection->executeQuery(sprintf('DROP DATABASE IF EXISTS %s', $databaseName));
        $connection->executeQuery(sprintf('CREATE DATABASE %s', $databaseName));
        $connection->close();

        $dependencyFactory = ConfigurationFactory::forMigrations($this->configuration, $this->connectionName);

        $input = new ArrayInput(['version' => 'latest']);
        $input->setInteractive(false);
        $output = new BufferedOutput();

        $command = new MigrateCommand($dependencyFactory);
        $command->run($input, $output);
    }

    /**
     * @throws Exception
     */
    public function executeAfterLastTest(): void
    {
        $input = new ArrayInput(['version' => 'first']);
        $input->setInteractive(false);
        $output = new BufferedOutput();

        $dependencyFactory = ConfigurationFactory::forMigrations($this->configuration, $this->connectionName);

        $command = new MigrateCommand($dependencyFactory);
        $command->run($input, $output);
    }

    public function getDbConnection(): string
    {
        // Use the env function if it exists
        if (function_exists('env')) {
            return env('DB_CONNECTION');
        }

        return getenv('DB_CONNECTION', true);
    }
}
