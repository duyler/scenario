<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

class ScenarioRespondData
{
    public function __construct(
        public string $scenarioId,
        /** @var ActionData[] */
        public array $actionData,
    ) {
    }
}
