<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Duyler\EventBusScenario\Enum\HandlerType;
use Duyler\EventBusScenario\Handler\ApiScenarioHandler;
use Duyler\EventBusScenario\Handler\WebScenarioHandler;

readonly class ScenarioHandlerMatcher
{
    public function __construct(
        private WebScenarioHandler $webScenarioHandler,
        private ApiScenarioHandler $apiScenarioHandler,
    ) {
    }

    public function match(HandlerType $handlerType): ScenarioHandlerInterface
    {
        return match($handlerType) {
            HandlerType::Web => $this->webScenarioHandler,
            HandlerType::Api => $this->apiScenarioHandler,
        };
    }
}
