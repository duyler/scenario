<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

class Scenario
{
    public function __construct(
        public string $id,
        public string $descriptions,
        public array $authors,
        /** @var ScenarioAction[] $actions */
        public array $actions,
    ) {
    }

    public static function fromArray(array $scenarioData): Scenario
    {
        $actions = [];
        foreach ($scenarioData['actions'] as $action) {
            $actions[] = ScenarioAction::fromArray($action);
        }

        return new self(
            id: $scenarioData['id'],
            descriptions: $scenarioData['description'] ?? '',
            authors: $scenarioData['authors'] ?? [],
            actions: $actions,
        );
    }
}
