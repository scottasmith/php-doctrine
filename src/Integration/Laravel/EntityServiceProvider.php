<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laravel;

use Bugsnag\Client;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as ServiceProviderAlias;
use ScottSmith\Doctrine\EntityManagerProvider;
use ScottSmith\Doctrine\Exception\ConfigurationException;
use ScottSmith\Doctrine\Logger\BugsnagLogger;

class EntityServiceProvider extends ServiceProviderAlias
{
    /**
     * @throws ConfigurationException
     */
    public function register()
    {
        if (!file_exists(config_path('doctrine.php'))) {
            // Not published config file yet?
            return;
        }

        $configuration = new Configuration();
        $dependencyResolver = new DependencyResolver($this->app);
        $managers = new EntityManagerProvider($configuration, $dependencyResolver);

        $this->app->instance(EntityManagerProvider::class, $managers);
        $this->app->singleton(EntityManagerInterface::class, function () use ($managers, $configuration) {
            return $managers->get($configuration->getDefault());
        });
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/cli-config.php' => base_path('cli-config.php')], 'config');
        $this->publishes([__DIR__ . '/config.php' => config_path('doctrine.php')], 'config');
    }
}
