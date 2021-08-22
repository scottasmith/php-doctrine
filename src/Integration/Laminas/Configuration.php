<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas;

use ScottSmith\Doctrine\Configuration\ConfigurationInterface;
use ScottSmith\Doctrine\Exception\ConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * @param array|null $config
     */
    public function __construct(private ?array $config = null)
    {
    }

    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function getConfiguration(): array
    {
        if (!$this->config) {
            $configPath = $this->getConfigPath();

            if (!is_file($configPath) || !is_readable($configPath)) {
                throw new ConfigurationException("Configuration file ${$configPath} not readable");
            }

            $this->config = require($configPath);
        }

        return $this->config;
    }

    /**
     * @return string
     * @throws ConfigurationException
     */
    public function getDefault(): string
    {
        $config = $this->getConfiguration();

        return $config['default'] ?? 'default';
    }

    /**
     * @return string
     */
    private function getConfigPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [getcwd(), 'config', 'autoload', 'doctrine.php']);
    }
}
