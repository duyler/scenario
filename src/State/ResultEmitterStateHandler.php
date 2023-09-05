<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\State;

use Duyler\EventBus\Contract\State\StateMainFinalHandlerInterface;
use Duyler\EventBus\State\Service\StateMainFinalService;
use Duyler\EventBusScenario\ActionData;
use Duyler\EventBusScenario\Context;
use Duyler\EventBusScenario\Enum\HandlerType;
use Duyler\EventBusScenario\ScenarioHandlerMatcher;
use Duyler\EventBusScenario\ScenarioRespondData;
use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Response\EmptyResponse;
use HttpSoft\Response\ResponseStatusCodeInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ResultEmitterStateHandler implements StateMainFinalHandlerInterface
{
    public function __construct(
        private Context $context,
        private ScenarioHandlerMatcher $handlerMatcher,
    ) {
    }

    public function prepare(): void
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function handle(StateMainFinalService $stateService): void
    {
        if ($this->context->isEmpty()) {
            $response = new EmptyResponse(ResponseStatusCodeInterface::STATUS_NOT_FOUND);
            $emitter = new SapiEmitter();
            $emitter->emit($response);
            return;
        }

        $scenario = $this->context->read();

        if (empty($scenario->handler)) {
            return;
        }

        $actionDataList = [];

        foreach ($scenario->actions as $action) {
            $actionResult = $stateService->getResult($action->id);
            if ($actionResult->data !== null) {
                $actionDataList[] = new ActionData(
                    $action->id,
                    $actionResult->data
                );
            }
        }

        $scenarioRespondData = new ScenarioRespondData(
            $scenario->id,
            $actionDataList
        );

        $handler = $this->handlerMatcher->match(HandlerType::from($scenario->handler));
        $response = $handler->handle($scenarioRespondData);

        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
