<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laminas;

use Doctrine\ORM\EntityManagerInterface;
use ScottSmith\Doctrine\EntityManagerProvider;
use ScottSmith\Doctrine\Integration\Laminas\Container\EntityManagerFactory;
use ScottSmith\Doctrine\Integration\Laminas\Container\EntityManagerProviderFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    EntityManagerProvider::class => EntityManagerProviderFactory::class,

                    EntityManagerInterface::class => EntityManagerFactory::class,
                ],
            ],
        ];
    }
}