<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas;

use Psr\Container\ContainerInterface;
use ScottSmith\Doctrine\DependencyResolverInterface;

class DependencyResolver implements DependencyResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $dependency
     * @return mixed
     */
    public function get(string $dependency)
    {
        return $this->container->get($dependency);
    }
}
