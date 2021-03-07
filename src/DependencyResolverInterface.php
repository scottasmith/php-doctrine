<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine;

interface DependencyResolverInterface
{
    /**
     * @param string $dependency
     * @return mixed
     */
    public function get(string $dependency);
}
