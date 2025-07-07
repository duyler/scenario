<?php

declare(strict_types=1);

namespace Duyler\Scenario;

use Duyler\Builder\Loader\LoaderServiceInterface;
use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Formatter\IdFormatter;
use Duyler\Router\Route;
use Duyler\Scenario\Exception\InvalidScenarioException;
use Duyler\Web\Enum\HttpMethod;
use UnitEnum;

final readonly class Scenario
{
    public string|UnitEnum $id;

    public string $hash;

    public ScenarioStep $step;

    public null|string $commandName;

    public null|string $commandAction;

    public function __construct(
        LoaderServiceInterface $loaderService,
        array $scenario,
    ) {
        $this->hash = spl_object_hash($this);

        if (false === isset($scenario['id'])) {
            throw new InvalidScenarioException('Scenario id is not set.');
        }

        $this->id = $scenario['id'];

        if (isset($scenario['reason']['route']['path'])) {
            $route = match ($scenario['reason']['route']['method']) {
                HttpMethod::Get => Route::get($scenario['reason']['route']['path']),
                HttpMethod::Post => Route::post($scenario['reason']['route']['path']),
                HttpMethod::Put => Route::put($scenario['reason']['route']['path']),
                HttpMethod::Delete => Route::delete($scenario['reason']['route']['path']),
                HttpMethod::Patch => Route::patch($scenario['reason']['route']['path']),
                default => throw new InvalidScenarioException(
                    'Invalid route method for scenario ' . IdFormatter::toString($scenario['id']),
                ),
            };

            $route->target($this->hash);
            $route->where($scenario['reason']['route']['where']);
        }

        if (isset($scenario['reason']['command'])) {
            $this->commandName =  $scenario['reason']['command'];

            $actionId = 'Scenario:' . IdFormatter::toString($this->id) . ':Command:' . $scenario['reason']['command'];

            $loaderService->addAction(
                new Action(
                    id: $actionId,
                    handler: function () {
                        return  $this;
                    },
                    type: Scenario::class,
                ),
            );

            $this->commandAction = $actionId;
        } else {
            $this->commandName = null;
            $this->commandAction = null;
        }

        $this->step = new ScenarioStep(
            scenarioId: $scenario['id'],
            validator: new ScenarioValidator($loaderService),
            scenario: $scenario['scenario'],
        );
    }
}
