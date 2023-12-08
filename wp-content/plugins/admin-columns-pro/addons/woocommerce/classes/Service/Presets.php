<?php

declare(strict_types=1);

namespace ACA\WC\Service;

use AC\Asset\Location\Absolute;
use AC\Registerable;

class Presets implements Registerable
{

    private $location;

    private $config;

    public function __construct(Absolute $location, array $config)
    {
        $this->location = $location;
        $this->config = $config;
    }

    public function register(): void
    {
        add_filter('acp/storage/preset/files', [$this, 'load_files']);
    }

    public function load_files(array $files): array
    {
        foreach ($this->config as $relative_file_path) {
            $files[] = $this->location->with_suffix($relative_file_path)->get_path();
        }

        return $files;
    }

}