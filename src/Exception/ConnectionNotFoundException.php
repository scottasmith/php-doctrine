<?php

declare(strict_types=1);

namespace ScottSmith\Doctrine\Exception;

use Exception;

class ConnectionNotFoundException extends Exception
{
    public static function forName(string $name): Exception
    {
        return new static("Doctrine connection ${name} not found");
    }
}
