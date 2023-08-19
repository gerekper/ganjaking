<?php

namespace ACA\JetEngine;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

class JetEngine implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! class_exists('Jet_Engine', false) || ! $this->check_minimum_jet_engine_version()) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Admin($this->location),
            new Service\ColumnInstantiate(),
            new Service\ColumnGroups(),
            new Service\RelationalColumns(),
            new Service\MetaColumns(),
            new IntegrationStatus('ac-addon-jetengine'),
        ]);
    }

    private function check_minimum_jet_engine_version(): bool
    {
        $jet_engine = jet_engine();

        return $jet_engine && version_compare($jet_engine->get_version(), '2.11.0', '>=');
    }

}