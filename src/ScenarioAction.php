<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

readonly class ScenarioAction
{
    public function __construct(
        public string $id,
        public string $description = '',
        public array $ifFail = [],
    ) {
    }

    public static function fromArray(array $scenarioActionData): ScenarioAction
    {
        return new self(
            id: $scenarioActionData['id'],
            description: $scenarioActionData['description'] ?? '',
            ifFail: $scenarioActionData['ifFail'] ?? [],
        );
    }
}
