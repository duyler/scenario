<?php

declare(strict_types=1);

namespace Duyler\EventBusScenario;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\EventBusScenario\Action\MakeRequestAction;
use Duyler\EventBusScenario\Provider\RouterRequestProvider;
use Duyler\EventBusScenario\State\ReceiveScenarioStateHandler;
use Duyler\EventBusScenario\State\RequestToActionStateHandler;
use Duyler\EventBusScenario\State\ResultEmitterStateHandler;
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

        $requestToAction = $loaderService->getContainer()->make(RequestToActionStateHandler::class);
        $receiveScenario = $loaderService->getContainer()->make(ReceiveScenarioStateHandler::class);
        $resultEmitter = $loaderService->getContainer()->make(ResultEmitterStateHandler::class);
        $loaderService->getBuilder()->addStateHandler($resultEmitter);
        $loaderService->getBuilder()->addStateHandler($receiveScenario);
        $loaderService->getBuilder()->addStateHandler($requestToAction);
        $loaderService->getBuilder()->doAction($makeRequestAction);
    }
}
