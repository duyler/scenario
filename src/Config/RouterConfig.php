<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Config;

readonly class RouterConfig
{
    public function __construct(
        public array $languages,
        public string $routesDirPath,
    ) {
    }
}
