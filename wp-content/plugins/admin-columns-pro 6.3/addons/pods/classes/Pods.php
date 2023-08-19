<?php

namespace ACA\Pods;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

class Pods implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! function_exists('pods') ||
             ! defined('PODS_VERSION') ||
             ! version_compare(PODS_VERSION, '2.7', '>=')) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Columns(),
            new Service\Scripts($this->location),
            new IntegrationStatus('ac-addon-pods'),
        ]);
    }

}