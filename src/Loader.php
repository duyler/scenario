<?php

declare(strict_types=1);

namespace Duyler\Scenario;

use Duyler\Builder\Loader\LoaderServiceInterface;
use Duyler\Builder\Loader\PackageLoaderInterface;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\Build\Context;
use Duyler\Scenario\State\HandleRunningScenarioStateHandler;
use Duyler\Scenario\State\RequestToScenarioStateHandler;
use Duyler\Scenario\State\ResolveCommandStateHandler;
use Duyler\Scenario\State\ResolveRouteStateHandler;
use FilesystemIterator;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;

class Loader implements PackageLoaderInterface
{
    public function __construct(
        private ScenarioConfig $scenarioConfig,
        private ScenarioResolver $scenarioResolver,
        private ContainerInterface $container,
    ) {}

    #[Override]
    public function beforeLoadBuild(LoaderServiceInterface $loaderService): void
    {
        /** @var RequestToScenarioStateHandler $requestToScenarioStateHandler */
        $requestToScenarioStateHandler = $this->container->get(RequestToScenarioStateHandler::class);

        /** @var HandleRunningScenarioStateHandler $handleRunningScenarioStateHandler */
        $handleRunningScenarioStateHandler = $this->container->get(HandleRunningScenarioStateHandler::class);

        /** @var ResolveRouteStateHandler $resolveRouteStateHandler */
        $resolveRouteStateHandler = $this->container->get(ResolveRouteStateHandler::class);

        /** @var ResolveCommandStateHandler $resolveCommandStateHandler */
        $resolveCommandStateHandler = $this->container->get(ResolveCommandStateHandler::class);

        $loaderService->addStateHandler($requestToScenarioStateHandler);
        $loaderService->addStateHandler($handleRunningScenarioStateHandler);
        $loaderService->addStateHandler($resolveRouteStateHandler);
        $loaderService->addStateHandler($resolveCommandStateHandler);
        $loaderService->addStateContext(
            new Context([
                RequestToScenarioStateHandler::class,
                HandleRunningScenarioStateHandler::class,
                ResolveRouteStateHandler::class,
                ResolveCommandStateHandler::class,
            ]),
        );
    }

    #[Override]
    public function afterLoadBuild(LoaderServiceInterface $loaderService): void
    {
        $iterator = new RecursiveIteratorIterator(
            iterator: new RecursiveDirectoryIterator($this->scenarioConfig->path, FilesystemIterator::SKIP_DOTS),
            mode: RecursiveIteratorIterator::SELF_FIRST,
            flags: RecursiveIteratorIterator::CATCH_GET_CHILD,
        );

        foreach ($iterator as $path => $dir) {
            if ($dir->isFile() && 'yaml' === strtolower($dir->getExtension())) {
                $scenario = Yaml::parseFile($path, Yaml::PARSE_CONSTANT);

                $this->scenarioResolver->resolve(new Scenario($loaderService, $scenario));
            }
        }
    }
}
