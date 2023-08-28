<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\State;

use Duyler\EventBus\Contract\State\StateMainFinalHandlerInterface;
use Duyler\EventBus\State\Service\StateMainFinalService;
use Duyler\EventBusScenario\Context;
use Duyler\TwigWrapper\TwigWrapper;
use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Response\EmptyResponse;
use HttpSoft\Response\HtmlResponse;
use HttpSoft\Response\ResponseStatusCodeInterface;
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
        if ($this->context->isEmpty()) {
            $response = new EmptyResponse(ResponseStatusCodeInterface::STATUS_NOT_FOUND);
            $emitter = new SapiEmitter();
            $emitter->emit($response);
            return;
        }

        $scenario = $this->context->read();

        $result = [];

        foreach ($scenario->actions as $action) {
            $segments = explode('.', $action->id);
            $domain = $segments[0];
            $propertyName = $segments[1];
            $result[$domain][$propertyName] = $stateService->getResult($action->id)->data;
        }

        $template = str_replace('.', DIRECTORY_SEPARATOR, $scenario->id);

        if ($this->twigWrapper->exists($template) === false) {
            return;
        }

        $content = $this->twigWrapper->content($result)->render($template);

        $response = new HtmlResponse($content);
        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
