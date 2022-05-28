<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Testing;

use Doctrine\DBAL\Connection;

/**
 * Database Helpers. Purposefully leaving out the PHPDoc for exceptions
 *
 * @method Connection getDoctrineConnection
 */
trait DatabaseHelperTrait
{
    /**
     * @param string $table
     * @param array $data
     * @return int
     */
    protected function insertDoctrineRow(string $table, array $data)
    {
        return $this->getDoctrineConnection()->insert($table, $data);
    }

    /**
     * @param string $table
     * @param array $queryParams
     */
    protected function assertDoctrineRowInDb(string $table, array $queryParams)
    {
        [$query, $params] = $this->selectFromDoctrine($table, $queryParams);

        $this->assertNotFalse(
            $this->getDoctrineConnection()->fetchAssociative(implode(' AND ', $query), $params),
            'Failed to assert row is in database'
        );
    }

    /**
     * @param string $table
     * @param array $queryParams
     */
    protected function assertDoctrineRowNotInDb(string $table, array $queryParams)
    {
        [$query, $params] = $this->selectFromDoctrine($table, $queryParams);

        $this->assertFalse(
            $this->getDoctrineConnection()->fetchAssociative(implode(' AND ', $query), $params),
            'Failed to assert row is not in database'
        );
    }

    /**
     * @param string $table
     * @param array $queryParams
     * @param int $count
     */
    protected function assertDoctrineCount(string $table, array $queryParams, int $count)
    {
        [$query, $params] = $this->selectFromDoctrine($table, $queryParams);

        $this->assertSame(
            $count,
            $this->getDoctrineConnection()->executeQuery(implode(' AND ', $query), $params)->rowCount(),
            'Failed to assert row is not in database'
        );
    }

    /**
     * @param string $table
     * @param array $queryParams
     * @param int $count
     */
    protected function assertNotDoctrineCount(string $table, array $queryParams, int $count)
    {
        [$query, $params] = $this->selectFromDoctrine($table, $queryParams);

        $this->assertNotSame(
            $count,
            $this->getDoctrineConnection()->executeQuery(implode(' AND ', $query), $params)->rowCount(),
            'Failed to assert row is not in database'
        );
    }

    /**
     * @param string $table
     * @param $queryParams
     * @return array
     */
    protected function fetchFromDoctrine(string $table, $queryParams)
    {
        [$query, $params] = $this->selectFromDoctrine($table, $queryParams);
        return $this->getDoctrineConnection()->fetchAssoc(implode(' AND ', $query), $params);
    }

    /**
     * @param string $table
     * @param array $queryParams
     * @return array[]
     */
    private function selectFromDoctrine(string $table, array $queryParams)
    {
        $connection = $this->getDoctrineConnection();
        $query = [sprintf('SELECT * FROM %s WHERE 1', $connection->quoteIdentifier($table))];
        $params = [];

        foreach ($queryParams as $key => $value) {
            if (null === $value) {
                $query[] = sprintf('%s IS NULL', $connection->quoteIdentifier($key));
            } elseif (true === $value) {
                    $query[] = sprintf('%s IS NOT NULL', $connection->quoteIdentifier($key));
            } elseif (is_array($value)) {
                $query[] = sprintf('JSON_CONTAINS(%s, ?)', $connection->quoteIdentifier($key));
                $params[] = json_encode($value);
            } elseif (is_string($value)) {
                $query[] = sprintf('%s LIKE ?', $connection->quoteIdentifier($key));
                $params[] = sprintf('%%%s%%', $value);
            } else {
                $query[] = sprintf('%s = ?', $connection->quoteIdentifier($key));
                $params[] = $value;
            }
        }

        return [$query, $params];
    }
}
