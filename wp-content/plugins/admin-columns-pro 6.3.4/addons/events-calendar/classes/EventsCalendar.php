<?php

declare(strict_types=1);

namespace ACA\EC;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

final class EventsCalendar implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! class_exists('Tribe__Events__Main', false)) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\ColumnGroups(),
            new Service\ListScreens(),
            new Service\Scripts($this->location),
            new Service\TableScreen($this->location),
            new IntegrationStatus('ac-addon-events-calendar'),
        ]);
    }

}