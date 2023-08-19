<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Asset\Location;
use AC\Registerable;
use AC\Services;
use ACP\ConditionalFormat\Settings\ListScreen\HideOnScreenFactory;

final class Addon implements Registerable
{

    private $location;

    private $rules_repository_factory;

    private $hide_on_screen_factory;

    public function __construct(
        Location\Absolute $location,
        RulesRepositoryFactory $rules_repository_factory,
        HideOnScreenFactory $hide_on_screen_factory
    ) {
        $this->location = $location;
        $this->rules_repository_factory = $rules_repository_factory;
        $this->hide_on_screen_factory = $hide_on_screen_factory;
    }

    public function register(): void
    {
        $this->create_services()
             ->register();
    }

    private function create_services(): Services
    {
        $operators = new Operators();

        return new Services([
            new Service\Assets(
                $this->location,
                $operators,
                $this->rules_repository_factory,
                $this->hide_on_screen_factory
            ),
            new Service\Formatter($operators, $this->rules_repository_factory),
            new Service\ListScreenSettings($this->hide_on_screen_factory),
            new Service\Storage($this->rules_repository_factory),
        ]);
    }

}