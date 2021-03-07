<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

abstract class AbstractLogger implements SQLLogger
{
    /**
     * @var array|null
     */
    private ?array $query = null;

    /** @var float|null */
    private ?float $start = null;

    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->start = microtime(true);

        $this->query = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ];
    }

    public function stopQuery()
    {
        $this->logQuery(
            $this->query['sql'],
            microtime(true) - $this->start,
            $this->query['params'],
            $this->query['types']
        );
    }

    abstract protected function logQuery(string $sql, float $time, ?array $params = null, ?array $types = null);
}
