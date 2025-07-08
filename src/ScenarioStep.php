<?php

declare(strict_types=1);

namespace Duyler\Scenario;

use DateInterval;
use Duyler\Scenario\Exception\ActionForScenarioNotExistsException;
use Duyler\Scenario\Exception\ActionScenarioNotSetException;
use UnitEnum;

readonly class ScenarioStep
{
    /** @var array<array-key, string|UnitEnum>  */
    public array $action;
    public null|ScenarioStep $success;
    public null|ScenarioStep $fail;
    public array $triggerFor;
    public null|string $scenario;
    public null|DateInterval $timeout;

    public function __construct(
        string|UnitEnum $scenarioId,
        ScenarioValidator $validator,
        array $scenario,
    ) {
        $actions = $scenario['start']
            ?? $scenario['next']
            ?? $scenario['end']
            ?? throw new ActionScenarioNotSetException($scenarioId);

        foreach ($actions as $action) {
            if ($validator->actionIsNotExists($action)) {
                throw new ActionForScenarioNotExistsException($scenarioId, $action, );
            }
        }

        $this->action = $actions;

        if (isset($scenario['success'])) {
            $this->success = new ScenarioStep($scenarioId, $validator, $scenario['success']);
        } else {
            $this->success = null;
        }

        if (isset($scenario['fail'])) {
            $this->fail = new ScenarioStep($scenarioId, $validator, $scenario['fail']);
        } else {
            $this->fail = null;
        }
    }
}
