<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Exception;

class TemplateNotFoundException extends Exception
{
    public function __construct(string $scenarioId)
    {
        parent::__construct('Template for' . $scenarioId . ' not found');
    }
}
