<?php

namespace ACA\BbPress;

use AC\Registerable;
use AC\Services;

class BbPress implements Registerable
{

    public function register(): void
    {
        if ( ! class_exists('bbPress')) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Columns(),
            new Service\Editing(),
            new Service\ListScreens(),
        ]);
    }

}