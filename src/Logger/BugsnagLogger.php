<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Logger;

use Bugsnag\Breadcrumbs\Breadcrumb;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

class BugsnagLogger extends AbstractLogger
{
    protected function logQuery(string $sql, float $time, ?array $params = null, ?array $types = null)
    {
        $data = ['sql' => $sql];

        if ($params) {
            foreach ($params as $index => $binding) {
                $data["binding {$index}"] = $binding;
            }
        }

        $data['time'] = "{$time}ms";

        Bugsnag::leaveBreadcrumb('Query executed', Breadcrumb::PROCESS_TYPE, $data);
    }
}
