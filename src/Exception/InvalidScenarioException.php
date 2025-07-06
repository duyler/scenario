<?php

declare(strict_types=1);

namespace Duyler\Scenario\Exception;

use Exception;

class InvalidScenarioException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
