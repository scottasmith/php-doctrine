<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas\Container;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use ScottSmith\Doctrine\EntityManagerProvider;

class EntityManagerFactory
{
    public function __invoke(ContainerInterface $container): EntityManagerInterface
    {
        $manager = $container->get(EntityManagerProvider::class);

        return $managers->get($configuration->getDefault());
    }
}