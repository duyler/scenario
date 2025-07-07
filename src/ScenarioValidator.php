<?php

declare(strict_types=1);

namespace Duyler\Scenario;

use Duyler\Builder\Loader\LoaderServiceInterface;
use UnitEnum;

final readonly class ScenarioValidator
{
    public function __construct(
        private LoaderServiceInterface $loader,
    ) {}

    public function actionIsNotExists(string|UnitEnum $action): bool
    {
        return false === $this->loader->actionIsExists($action);
    }
}
