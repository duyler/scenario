<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

readonly class ActionData
{
    public function __construct(
        public string $actionId,
        public object $data,
    ) {
    }
}
