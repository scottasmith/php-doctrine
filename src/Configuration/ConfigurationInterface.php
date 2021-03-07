<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Configuration;

interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @return string
     */
    public function getDefault(): string;
}
