<?php

declare(strict_types=1);

namespace Duyler\Scenario\State;

use Duyler\EventBus\Contract\State\MainAfterStateHandlerInterface;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBus\State\StateContext;
use Duyler\Scenario\Scenario;
use Duyler\Scenario\ScenarioStep;
use Override;

class HandleRunningScenarioStateHandler implements MainAfterStateHandlerInterface
{
    #[Override]
    public function handle(StateMainAfterService $stateService, StateContext $context): void
    {
        /** @var ScenarioStep $currentStep */
        $currentStep = $context->read('currentStep');

        foreach ($currentStep->action as $action) {
            if ($stateService->resultIsExists($action)) {
                $result = $stateService->getResult($action);
                if (ResultStatus::Fail === $result->status) {
                    if (null !== $currentStep->fail) {
                        foreach ($currentStep->fail->action as $nextAction) {
                            if ($stateService->actionIsExists($nextAction)) {
                                $stateService->doExistsAction($nextAction);
                            }
                        }
                        $context->write('currentStep', $currentStep->fail);
                        return;
                    }
                }
            } else {
                return;
            }
        }

        if (null !== $currentStep->success) {
            foreach ($currentStep->success->action as $nextAction) {
                if ($stateService->actionIsExists($nextAction)) {
                    $stateService->doExistsAction($nextAction);
                }
            }

            $context->write('currentStep', $currentStep->success);
            return;
        }

        $context->write('singleScenario', null);
        $context->write('currentStep', null);
    }

    #[Override]
    public function observed(StateContext $context): array
    {
        /** @var null|Scenario $scenario */
        $scenario = $context->read('singleScenario');

        if (null === $scenario) {
            return [microtime()];
        }

        /** @var ScenarioStep $currentStep */
        $currentStep = $context->read('currentStep');

        return $currentStep->action;
    }
}
