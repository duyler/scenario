<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Handler;

use Duyler\EventBusScenario\ScenarioRespondData;
use Duyler\EventBusScenario\ScenarioHandlerInterface;
use Duyler\EventBusScenario\TemplateNotFoundException;
use Duyler\TwigWrapper\TwigWrapper;
use HttpSoft\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class WebScenarioHandler implements ScenarioHandlerInterface
{
    public function __construct(private TwigWrapper $twigWrapper)
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws TemplateNotFoundException
     */
    public function handle(ScenarioRespondData $scenarioRespondData): ResponseInterface
    {
        $result = [];

        foreach ($scenarioRespondData->actionData as $action) {
            $segments = explode('.', $action->actionId);
            $domain = $segments[0];
            $propertyName = $segments[1];
            $result[$domain][$propertyName] = $action->data;
        }

        $template = str_replace('.', DIRECTORY_SEPARATOR, $scenarioRespondData->scenarioId);

        if ($this->twigWrapper->exists($template) === false) {
            throw new TemplateNotFoundException($scenarioRespondData->scenarioId);
        }

        $content = $this->twigWrapper->content($result)->render($template);
        return new HtmlResponse($content);
    }
}
