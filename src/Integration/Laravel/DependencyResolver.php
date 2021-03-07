<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laravel;

use Illuminate\Contracts\Foundation\Application;
use ScottSmith\Doctrine\DependencyResolverInterface;

class DependencyResolver implements DependencyResolverInterface
{
    /**
     * @var Application
     */
    private Application $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $dependency
     * @return mixed
     */
    public function get(string $dependency)
    {
        return $this->app->get($dependency);
    }
}
