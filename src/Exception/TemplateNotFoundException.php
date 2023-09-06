<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Exception;

use Exception;

class TemplateNotFoundException extends Exception
{
    public function __construct(string $template, string $scenarioId)
    {
        parent::__construct('Template ' . $template  . ' for ' . $scenarioId . ' not found');
    }
}
