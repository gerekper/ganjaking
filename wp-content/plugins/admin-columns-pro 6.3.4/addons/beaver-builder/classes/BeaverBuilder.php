<?php

namespace ACA\BeaverBuilder;

use AC;
use AC\Registerable;
use AC\Services;
use ACA\BeaverBuilder\Service;

class BeaverBuilder implements Registerable
{

    public function register(): void
    {
        if ( ! class_exists('FLBuilderLoader')) {
            return;
        }

        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\Templates());
        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\SavedColumns());
        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\SavedModules());
        AC\ListScreenFactory\Aggregate::add(new ListScreenFactory\SavedRows());

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\ListScreens(),
            new Service\PostTypes(),
        ]);
    }

}