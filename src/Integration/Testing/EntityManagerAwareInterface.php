<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Testing;

use Doctrine\ORM\EntityManagerInterface;

interface EntityManagerAwareInterface
{
    public function getEntityManager() : EntityManagerInterface;
}
