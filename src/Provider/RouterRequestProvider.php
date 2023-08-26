<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario\Provider;

use Duyler\DependencyInjection\Provider\AbstractProvider;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

class RouterRequestProvider extends AbstractProvider
{
    private ServerRequestInterface $request;

    public function __construct()
    {
        $this->request = ServerRequestCreator::create();
    }

    public function getParams(): array
    {
        return [
            'uri' => $this->request->getUri()->getPath(),
            'method' => $this->request->getMethod(),
            'host' => $this->request->getUri()->getHost(),
            'protocol' => $this->request->getUri()->getScheme(),
        ];
    }
}
