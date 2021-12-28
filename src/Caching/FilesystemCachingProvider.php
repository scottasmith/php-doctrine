<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Caching;

use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FilesystemCachingProvider implements DoctrineCachingProviderInterface
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return CacheProvider
     */
    public function getCachingProvider(): CacheProvider
    {
        return new DoctrineProvider(new FilesystemAdapter('', 0, $this->path));
    }
}

