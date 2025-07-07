<?php

declare(strict_types=1);

namespace Duyler\Scenario\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Duyler\Http\Action\Router;
use Duyler\Router\CurrentRoute;
use Duyler\Scenario\ScenarioStorage;
use Override;

class RequestToScenarioStateHandler implements MainAfterStateHandlerInterface
{
    public function __construct(
        private ScenarioStorage $scenarioStorage,
    ) {}

    #[Override]
    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        /**
         * @var CurrentRoute $currentRoute
         */
        $currentRoute = $stateService->getResultData();

        if (null === $currentRoute->target) {
            return;
        }

        $scenario = $this->scenarioStorage->getByHash($currentRoute->target);

        if (null === $scenario) {
            return;
        }

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
        return [Router::GetRoute];
    }
}
