<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Duyler\Config\Config;
use Duyler\EventBus\Contract\State\StateMainAfterHandlerInterface;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Psr\Http\Message\ServerRequestInterface;

readonly class RequestDoActionStateHandler implements StateMainAfterHandlerInterface
{
    public function __construct(private Config $config, private ScenarioParser $scenarioParser)
    {
    }

    public function handle(StateMainAfterService $stateService): void
    {
        /** @var ServerRequestInterface $request */
        $request = $stateService->resultData;

        if (empty($request->getAttribute('action')) === false) {
            $stateService->doExistsAction($request->getAttribute('action'));
            return;
        }

        $resource = strtolower(str_replace('.', '/', $request->getAttribute('scenario')));

        $resource = strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/',
            '_', $resource
        ));

        $scenarioResource = $this->config->env(Config::PROJECT_ROOT)
            . $this->config->get('scenario', 'scenario_dir_path')
            . DIRECTORY_SEPARATOR
            . $resource . '.json';

        if (is_file($scenarioResource)) {
            $scenario = $this->scenarioParser->parse($scenarioResource);

            foreach ($scenario->actions as $action) {
                $stateService->doExistsAction($action->id);
            }
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
