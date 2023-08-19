<?php

namespace ACA\Polylang;

use AC\Registerable;
use AC\Services;

class Polylang implements Registerable
{

    public function register(): void
    {
        if ( ! defined('POLYLANG_VERSION')) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Columns(),
            new Service\Table(),
        ]);
    }

}