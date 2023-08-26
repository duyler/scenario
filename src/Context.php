<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

class Context
{
    private ?Scenario $scenario = null;

    public function write(Scenario $scenario): void
    {
        $this->scenario = $scenario;
    }

    public function read(): Scenario
    {
        return $this->scenario;
    }

    public function isEmpty(): bool
    {
        return $this->scenario === null;
    }
}
