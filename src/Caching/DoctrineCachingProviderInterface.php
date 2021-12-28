<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Caching;

use Doctrine\Common\Cache\CacheProvider;

interface DoctrineCachingProviderInterface
{
    /**
     * @return CacheProvider
     */
    public function getCachingProvider(): CacheProvider;
}

