<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Psr\Http\Message\ResponseInterface;

interface ScenarioHandlerInterface
{
    public function handle(ScenarioRespondData $scenarioRespondData): ResponseInterface;
}
