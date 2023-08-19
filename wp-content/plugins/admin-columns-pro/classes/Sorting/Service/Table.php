<?php

declare(strict_types=1);

namespace ACP\Sorting\Service;

use AC;
use AC\ColumnRepository;
use AC\Registerable;
use ACP\Sorting;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortableFactory;
use ACP\Sorting\Settings\ListScreen\PreferredSort;

class Table implements Registerable
{

    private $location;

    private $native_sortable_factory;

    private $model_factory;

    public function __construct(
        AC\Asset\Location\Absolute $location,
        NativeSortableFactory $native_sortable_factory,
        ModelFactory $model_factory
    ) {
        $this->location = $location;
        $this->native_sortable_factory = $native_sortable_factory;
        $this->model_factory = $model_factory;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'init_table'], 11); // After filtering
    }

    public function init_table(AC\ListScreen $list_screen): void
    {
        $table = new Sorting\Table\Screen(
            $list_screen,
            $this->location,
            $this->native_sortable_factory->create($list_screen),
            $this->model_factory,
            new ColumnRepository($list_screen),
            new PreferredSort($list_screen)
        );

        $table->register();
    }

}