<?php

namespace ACA\BP;

use AC;
use AC\ListScreenFactory\Aggregate;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

final class BuddyPress implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! class_exists('BuddyPress', false)) {
            return;
        }

        Aggregate::add(new ListScreenFactory\Email());
        Aggregate::add(new ListScreenFactory\Group());

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Admin($this->location),
            new Service\Columns(),
            new Service\ListScreens(),
            new Service\Table($this->location),
            new IntegrationStatus('ac-addon-buddypress'),
        ]);
    }

}