<?php

namespace ACA\MetaBox;

use AC;
use AC\Registerable;
use AC\Services;
use ACP\Service\IntegrationStatus;
use MB_Comment_Meta_Box;

class MetaBox implements Registerable
{

    private $location;

    public function __construct(AC\Asset\Location\Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        if ( ! class_exists('RWMB_Loader', false)) {
            return;
        }

        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Service\Columns(new ColumnFactory(), new RelationColumnFactory(), new RelationshipRepository()),
            new Service\ColumnInstantiate(new RelationshipRepository()),
            new Service\QuickAdd(),
            new Service\ListScreens(),
            new Service\Scripts($this->location),
            new Service\Storage(),
            new IntegrationStatus('ac-addon-metabox'),
        ]);
    }

}