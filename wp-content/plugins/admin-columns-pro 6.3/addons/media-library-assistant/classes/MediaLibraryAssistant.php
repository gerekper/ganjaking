<?php

declare(strict_types=1);

namespace ACA\MLA;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;

class MediaLibraryAssistant implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! defined('MLA_PLUGIN_PATH')) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Admin($this->location),
            new Service\ColumnGroup(),
            new Service\Editing(),
            new Service\Export(),
            new Service\ListScreens(),
            new Service\TableScreen($this->location),
            new IntegrationStatus('ac-addon-media-library-assistant'),
        ]);
    }

}