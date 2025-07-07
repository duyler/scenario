<?php

declare(strict_types=1);

namespace Duyler\Scenario\Exception;

use Duyler\EventBus\Formatter\IdFormatter;
use Exception;
use UnitEnum;

class ActionForScenarioNotExistsException extends Exception
{
    public function __construct(string|UnitEnum $scenarioId, string|UnitEnum $action)
    {
        $message = 'Action "'
            . IdFormatter::toString($action)
            . '" for scenario "'
            . IdFormatter::toString($scenarioId)
            . '" is not exists.';

        parent::__construct($message);
    }
}
