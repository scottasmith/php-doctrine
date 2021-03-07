<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Integration\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider as ServiceProviderAlias;

class QueueServiceProvider extends ServiceProviderAlias
{
    public function boot()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->app->get(EntityManagerInterface::class);

        /** @var QueueManager */
        $queueManager = $this->app->get(QueueManager::class);
        $queueManager->before(function (JobProcessing $event) use ($em) {
            $em->getDoctrineConnection()->connect();
        });
    }
}
