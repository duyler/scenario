<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\State;

use Duyler\EventBus\Contract\State\StateMainFinalHandlerInterface;
use Duyler\EventBus\State\Service\StateMainFinalService;
use Duyler\EventBusScenario\Context;
use Duyler\TwigWrapper\TwigWrapper;
use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Response\HtmlResponse;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ResultEmitterStateHandler implements StateMainFinalHandlerInterface
{
    public function __construct(
        private Context $context,
        private TwigWrapper $twigWrapper,
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
        $scenario = $this->context->read();

        $result = [];

        foreach ($scenario->actions as $action) {
            $result[str_replace('.', '_', $action->id)] = $stateService->getResult($action->id)->data;
        }

        $content = $this->twigWrapper->content($result)->render($scenario->id);

        $response = new HtmlResponse($content);
        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
