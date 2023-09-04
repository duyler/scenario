<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

class ScenarioParser
{
    public function parse(string $resource): array
    {
        $content = [];

        $data = file_get_contents($resource);

        if ($data) {
            $content = json_decode(json: $data, associative: true, flags: JSON_THROW_ON_ERROR);
        }

        return $content;
    }
}
