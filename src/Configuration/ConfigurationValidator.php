<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Configuration;

use ScottSmith\Doctrine\Exception\ConfigurationException;

class ConfigurationValidator
{
    /**
     * @param array $configuration
     * @throws ConfigurationException
     */
    public static function validateConnections(array $configuration)
    {
        if (empty($configuration['connections'])) {
            throw new ConfigurationException('There are no Doctrine connections available');
        }

        foreach ($configuration['connections'] as $name => $connection) {
            if (!is_string($name)) {
                throw new ConfigurationException('The Doctrine connection key must be a string');
            }

            if (isset($connection['driver']) && !is_string($connection['driver'])) {
                throw new ConfigurationException("Doctrine driver is not a string in connection ${name}");
            }

            if (!isset($connection['host']) || empty($connection['host'])) {
                throw new ConfigurationException("Doctrine host is missing or invalid in connection ${name}");
            }

            if (!isset($connection['username']) ||empty($connection['username'])) {
                throw new ConfigurationException("Doctrine username is missing or invalid in connection ${name}");
            }

            if (!isset($connection['port']) || !is_numeric($connection['port'])) {
                throw new ConfigurationException("Doctrine port is missing or invalid in connection ${name}");
            }

            if (isset($connection['paths']) && !is_array($connection['paths'])) {
                throw new ConfigurationException("Doctrine proxies is not an array in connection ${name}");
            }

            if (isset($connection['proxies']) && !is_string($connection['proxies'])) {
                throw new ConfigurationException("Doctrine proxies is not a string in connection ${name}");
            }
        }
    }
}
