<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Action;

use Duyler\EventBus\Dto\Result;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\Router\Router;
use HttpSoft\ServerRequest\ServerRequestCreator;

readonly class MakeRequestAction
{
    public function __construct(private Router $router)
    {
    }

    public function __invoke(): Result
    {
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
