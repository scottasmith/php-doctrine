<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Testing;

use Doctrine\DBAL\Connection;

interface ConnectionAwareInterface
{
    /**
     * @param null $connection
     * @return Connection
     */
    public function getDoctrineConnection($connection = null): Connection;
}
