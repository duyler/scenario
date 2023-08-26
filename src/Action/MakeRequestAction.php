<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Action;

use Duyler\Config\Config;
use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\Router;
use HttpSoft\ServerRequest\ServerRequestCreator;

readonly class MakeRequestAction
{
    public function __construct(private Router $router, private Config $config)
    {
    }

    public function __invoke(): Result
    {
        $this->router->setRoutesDirPath(
            $this->config->env(Config::PROJECT_ROOT)
            . $this->config->get('router', 'routes_dir')
            . DIRECTORY_SEPARATOR
        );

        $result = $this->router->startRouting();

        $request = ServerRequestCreator::create();

        if ($result->status) {
            foreach ($result->attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            $request = $request
                ->withAttribute('handler', $result->handler)
                ->withAttribute('scenario', $result->scenario)
                ->withAttribute('action', $result->action)
                ->withAttribute('language', $result->language);

            return new Result(
                status: ResultStatus::Success,
                data: $request
            );
        }

        return new Result(
            status: ResultStatus::Fail,
            data: $request
        );
    }
}
