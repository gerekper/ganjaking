<?php

declare(strict_types=1);

namespace ACP\Filtering\Service\Table;

use AC\ListScreen;
use AC\Registerable;
use ACP\Filtering\Settings;
use ACP\Filtering\TableScreenFactory;
use ACP\Search\ComparisonFactory;

class FilterContainers implements Registerable
{

    private $comparison_factory;

    public function __construct(ComparisonFactory $comparison_factory)
    {
        $this->comparison_factory = $comparison_factory;
    }

    public function register(): void
    {
        add_action('ac/table/list_screen', [$this, 'load']);
    }

    public function load(ListScreen $list_screen): void
    {
        foreach ($list_screen->get_columns() as $column) {
            $setting = $column->get_setting('filter');

            if ( ! $setting instanceof Settings || ! $setting->is_active()) {
                continue;
            }

            $comparison = $this->comparison_factory->create($column);

            if ( ! $comparison) {
                continue;
            }

            $table = (new TableScreenFactory())->create(
                $list_screen,
                $column->get_name()
            );

            if ( ! $table) {
                continue;
            }

            $table->register();
        }
    }

}