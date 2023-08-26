<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\EventBusScenario\Action\MakeRequestAction;
use Duyler\EventBusScenario\Provider\RouterRequestProvider;
use Duyler\Router\Request;
use Psr\Http\Message\ServerRequestInterface;

class Loader implements PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void
    {
        $makeRequestAction = new Action(
            id: 'Request.MakeRequest',
            handler: MakeRequestAction::class,
            providers: [
                Request::class => RouterRequestProvider::class,
            ],
            externalAccess: true,
            contract: ServerRequestInterface::class,
        );

        $stateHandler = $loaderService->getContainer()->make(RequestDoActionStateHandler::class);
        $loaderService->getBuilder()->addStateHandler($stateHandler);
        $loaderService->getBuilder()->doAction($makeRequestAction);
    }
}
