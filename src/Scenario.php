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
            $action = ScenarioAction::fromArray($action);
            $actions[$action->id] = $action;
        }

        return new self(
            id: $scenarioData['id'],
            descriptions: $scenarioData['description'] ?? '',
            authors: $scenarioData['authors'] ?? [],
            actions: $actions,
        );
    }

    public function isParticipant(string $actionId): bool
    {
        return array_key_exists($actionId, $this->actions);
    }

    public function getScenarioAction(string $actionId): ScenarioAction
    {
        return $this->actions[$actionId];
    }
}
