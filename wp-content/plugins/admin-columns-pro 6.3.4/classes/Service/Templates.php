<?php

namespace ACP\Service;

use AC\Asset\Location\Absolute;
use AC\Registerable;

class Templates implements Registerable
{

    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        add_filter('ac/view/templates', [$this, 'templates']);
    }

    public function templates(array $templates): array
    {
        $templates[] = $this->location->with_suffix('templates')->get_path();

        return $templates;
    }

}