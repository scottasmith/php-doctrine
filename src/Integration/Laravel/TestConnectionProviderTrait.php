<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laravel;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ScottSmith\Doctrine\EntityManagerProvider;
use ScottSmith\Doctrine\Exception\ConfigurationException;
use ScottSmith\Doctrine\Exception\ConnectionNotFoundException;

trait TestConnectionProviderTrait
{
    /**
     * @var EntityManager|null
     */
    protected ?EntityManager $entityManagerCache = null;

    /**
     * @param string|null $connection
     * @return EntityManagerInterface
     */
    public function getEntityManager($connection = null): EntityManagerInterface
    {
        if (!$this->entityManagerCache) {
            if (!$connection) {
                $connection = env('DB_CONNECTION');
            }
            $configuration = new Configuration();
            $managers = new EntityManagerProvider($configuration);

            $this->entityManagerCache = $managers->get($connection);
        }

        return $this->entityManagerCache;
    }

    /**
     * @param string|null $connection
     * @return Connection
     */
    public function getConnection($connection = null): Connection
    {
        return $this->getEntityManager($connection)->getConnection();
    }
}
