<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Provider;

use Duyler\DependencyInjection\Provider\AbstractProvider;
use Duyler\Router\RouterConfig;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

class RouterRequestProvider extends AbstractProvider
{
    private ServerRequestInterface $request;

    public function __construct(private readonly RouterConfig $routerConfig)
    {
    }

    public function getParams(): array
    {
        return [
            'serverRequest' => ServerRequestCreator::create(),
            'routerConfig' => $this->routerConfig,
        ];
    }
}
