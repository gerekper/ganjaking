<?php

declare(strict_types=1);

namespace ACA\YoastSeo;

use AC;
use AC\Registerable;
use AC\Services;
use ACA\YoastSeo\Service;
use ACP\Service\IntegrationStatus;

class YoastSeo implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! defined('WPSEO_VERSION')) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Admin($this->location),
            new Service\ColumnGroups(),
            new Service\Columns(),
            new Service\HideFilters(),
            new Service\Table(),
            new IntegrationStatus('ac-addon-yoast-seo'),
        ]);
    }

}