<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\State;

use Duyler\EventBus\Contract\State\StateMainAfterHandlerInterface;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBusScenario\Context;

readonly class ReceiveScenarioStateHandler implements StateMainAfterHandlerInterface
{
    public function __construct(private Context $context)
    {
    }

    public function observed(): array
    {
        return [];
    }

    public function prepare(): void
    {
    }

    public function handle(StateMainAfterService $stateService): void
    {
        if ($this->context->isEmpty()) {
            return;
        }

        $scenario = $this->context->read();

        if ($scenario->isParticipant($stateService->actionId)) {
            if ($stateService->resultStatus === ResultStatus::Fail) {
                $action = $scenario->getScenarioAction($stateService->actionId);
                foreach ($action->ifFail as $actionId) {
                    $stateService->doExistsAction($actionId);
                }
            }
        }
    }
}
