<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laravel;

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Env;
use ScottSmith\Doctrine\Configuration\ConfigurationInterface;
use ScottSmith\Doctrine\Exception\ConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     * @throws ConfigurationException
     */
    public function getConfiguration(): array
    {
        if (!$this->isApplicationBootstrapped()) {
            Dotenv::create(Env::getRepository(), getcwd(), '.env')->safeLoad();
        }

        $configPath = $this->getConfigPath();

        if (!is_file($configPath) || !is_readable($configPath)) {
            throw new ConfigurationException("Configuration file ${$configPath} not readable");
        }

        return require($configPath);
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
        if ($this->isApplicationBootstrapped()) {
            return config_path('doctrine.php');
        } else {
            return implode(DIRECTORY_SEPARATOR, [getcwd(), 'config', 'doctrine.php']);
        }
    }

    /**
     * Whether or not Laravel has been bootstrapped
     *
     * @return bool
     */
    private function isApplicationBootstrapped(): bool
    {
        return (Container::getInstance() instanceof Application);
    }
}
