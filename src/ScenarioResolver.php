<?php

declare(strict_types=1);

namespace Duyler\Scenario;

use Duyler\Console\CommandCollector;

class ScenarioResolver
{
    public function __construct(
        private ScenarioStorage $scenarioStorage,
        private CommandCollector $commandCollector,
    ) {}

    public function resolve(Scenario $scenario): void
    {
        $this->scenarioStorage->add($scenario);

        if (null !== $scenario->commandName) {
            $this->commandCollector->add($scenario->commandName, $scenario->commandAction);
        }
    }
}
