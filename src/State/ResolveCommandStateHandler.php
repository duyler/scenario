<?php

declare(strict_types=1);

namespace Duyler\Scenario\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Duyler\Scenario\Scenario;
use Duyler\Scenario\ScenarioStorage;
use Override;

class ResolveCommandStateHandler implements MainAfterStateHandlerInterface
{
    public function __construct(
        private ScenarioStorage $scenarioStorage,
    ) {}

    #[Override]
    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        /** @var Scenario $scenario */
        $scenario = $stateService->getResultData();

        foreach ($scenario->step->action as $action) {
            if ($stateService->actionIsExists($action)) {
                $stateService->doExistsAction($action);
            }
        }

        $context->write('singleScenario', $scenario);
        $context->write('currentStep', $scenario->step);
    }

    #[Override]
    public function observed(StateContext $context): array
    {
        $scenarios = $this->scenarioStorage->getAllByAction();

        if (0 === count($scenarios)) {
            return [microtime()];
        }

        return array_keys($this->scenarioStorage->getAllByAction());
    }
}
