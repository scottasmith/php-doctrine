<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Logger;

use Psr\Log\LoggerInterface;

class PsrLogger extends AbstractLogger
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * PsrLogger constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function logQuery(string $sql, float $time, ?array $params = null, ?array $types = null)
    {
        $data = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'executionMs' => $time,
        ];

        $this->logger->debug('Query executed', $data);
    }
}
