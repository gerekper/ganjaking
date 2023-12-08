<?php

namespace ACA\EC\Service;

use AC\Asset\Location\Absolute;
use AC\Registerable;

final class Presets implements Registerable
{

    private $location;

    public function __construct(
        Absolute $location
    ) {
        $this->location = $location;
    }

    public function register(): void
    {
        add_filter('acp/storage/preset/files', [$this, 'add_presets']);
    }

    public function add_presets($files): array
    {
        $files[] = $this->location->with_suffix('config/presets/events-overview.json')->get_path();
        $files[] = $this->location->with_suffix('config/presets/events-venue.json')->get_path();
        $files[] = $this->location->with_suffix('config/presets/organizer-contact.json')->get_path();
        $files[] = $this->location->with_suffix('config/presets/venue-information.json')->get_path();

        return $files;
    }
}