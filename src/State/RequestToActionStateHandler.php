<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\State;

use Duyler\Config\Config;
use Duyler\EventBus\Contract\State\StateMainAfterHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\EventBusScenario\Context;
use Duyler\EventBusScenario\Scenario;
use Duyler\EventBusScenario\ScenarioParser;
use JsonException;
use Psr\Http\Message\ServerRequestInterface;

readonly class RequestToActionStateHandler implements StateMainAfterHandlerInterface
{
    public function __construct(
        private Config $config,
        private ScenarioParser $scenarioParser,
        private Context $context,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function handle(StateMainAfterService $stateService): void
    {
        /** @var ServerRequestInterface $request */
        $request = $stateService->resultData;

        if (empty($request->getAttribute('action')) === false) {
            $stateService->doExistsAction($request->getAttribute('action'));
            return;
        }

        if (empty($request->getAttribute('scenario'))) {
            return;
        }

        $resource = str_replace('.', '/', $request->getAttribute('scenario'));

        $resource = strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/',
            '_',
            $resource
        ));

        $scenarioResource = $this->config->env(Config::PROJECT_ROOT)
            . $this->config->get('scenario', 'scenario_dir_path')
            . DIRECTORY_SEPARATOR
            . $resource . '.json';

        if (is_file($scenarioResource)) {

            $scenarioData = $this->scenarioParser->parse($scenarioResource);
            $scenarioData['handler'] = $request->getAttribute('handler');

            $scenario = Scenario::fromArray($scenarioData);

            foreach ($scenario->actions as $action) {
                $stateService->doExistsAction($action->id);
            }

            $this->context->write($scenario);
        }
    }

    public function observed(): array
    {
        return ['Request.MakeRequest'];
    }

    public function prepare(): void
    {
    }
}
