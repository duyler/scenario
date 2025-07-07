<?php

declare(strict_types=1);

namespace Duyler\Scenario;

readonly class ScenarioConfig
{
    public function __construct(
        public string $path = 'scenarios',
    ) {}
}
