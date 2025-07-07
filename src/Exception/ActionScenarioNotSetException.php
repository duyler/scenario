<?php

declare(strict_types=1);

namespace Duyler\Scenario\Exception;

use Duyler\EventBus\Formatter\IdFormatter;
use Exception;
use UnitEnum;

class ActionScenarioNotSetException extends Exception
{
    public function __construct(string|UnitEnum $scenarioId)
    {
        $message = 'Action for step not set for ' . IdFormatter::toString($scenarioId);
        parent::__construct($message);
    }
}
