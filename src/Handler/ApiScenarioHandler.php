<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Handler;

use Duyler\EventBusScenario\ScenarioHandlerInterface;
use Duyler\EventBusScenario\ScenarioRespondData;
use HttpSoft\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class ApiScenarioHandler implements ScenarioHandlerInterface
{
    public function handle(ScenarioRespondData $scenarioRespondData): ResponseInterface
    {
        return new JsonResponse($scenarioRespondData);
    }
}
