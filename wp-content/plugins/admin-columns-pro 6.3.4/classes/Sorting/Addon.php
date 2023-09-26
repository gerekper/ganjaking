<?php

namespace ACP\Sorting;

use AC\Asset\Location\Absolute;
use AC\ListScreenFactory;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Services;
use ACP\Sorting\Service\ColumnSettings;
use ACP\Sorting\Service\Table;

class Addon implements Registerable
{

    private $storage;

    private $location;

    private $list_screen_factory;

    public function __construct(Storage $storage, Absolute $location, ListScreenFactory $list_screen_factory)
    {
        $this->storage = $storage;
        $this->location = $location;
        $this->list_screen_factory = $list_screen_factory;
    }

    public function register(): void
    {
        $this->create_services()->register();
    }

    private function create_services(): Services
    {
        return new Services([
            new Controller\ResetSorting(),
            new Controller\AjaxResetSorting($this->storage, $this->list_screen_factory),
            new Table(
                $this->location,
                new NativeSortableFactory(),
                new ModelFactory()
            ),
            new ColumnSettings(
                new ModelFactory(),
                new NativeSortableFactory()
            ),
        ]);
    }

}