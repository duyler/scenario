<?php

declare(strict_types=1);

namespace Duyler\Scenario;

class ScenarioStorage
{
    private array $byHash = [];

    private array $byAction = [];

    public function add(Scenario $scenario): void
    {
        $this->byHash[$scenario->hash] = $scenario;

        if (null !== $scenario->commandAction) {
            $this->byAction[$scenario->commandAction] = $scenario;
        }
    }

    public function getByHash(string $hash): ?Scenario
    {
        return $this->byHash[$hash] ?? null;
    }

    public function getByAction(string $actionId): ?Scenario
    {
        return $this->byAction[$actionId] ?? null;
    }

    public function getAllByHash(): array
    {
        return $this->byHash;
    }

    public function getAllByAction(): array
    {
        return $this->byAction;
    }
}
