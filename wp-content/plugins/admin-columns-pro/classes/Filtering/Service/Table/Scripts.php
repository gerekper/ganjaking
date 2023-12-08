<?php

declare(strict_types=1);

namespace ACP\Filtering\Service\Table;

use AC;
use AC\Asset;
use AC\Registerable;
use ACP\Filtering\Asset\TableScriptFactory;
use ACP\Filtering\OptionsFactory;
use ACP\Filtering\Settings;
use ACP\Search\ComparisonFactory;
use ACP\Settings\ListScreen\HideOnScreen;

class Scripts implements Registerable
{

    private $location;

    private $comparison_factory;

    private $options_factory;

    private $request;

    public function __construct(
        Asset\Location\Absolute $location,
        ComparisonFactory $comparison_factory,
        OptionsFactory $options_factory,
        AC\Request $request
    ) {
        $this->location = $location;
        $this->comparison_factory = $comparison_factory;
        $this->options_factory = $options_factory;
        $this->request = $request;
    }

    public function register(): void
    {
        add_action('ac/table_scripts', [$this, 'scripts'], 1);
    }

    public function scripts(AC\ListScreen $list_screen): void
    {
        if ( ! $this->is_enabled($list_screen)) {
            return;
        }

        $style = new Asset\Style('acp-filtering-table', $this->location->with_suffix('assets/filtering/css/table.css'));
        $style->enqueue();

        $script = (new TableScriptFactory(
            $this->location,
            $this->comparison_factory,
            $this->options_factory,
            $this->request
        ))->create(
            $list_screen
        );
        $script->enqueue();
    }

    private function is_enabled(AC\ListScreen $list_screen): bool
    {
        $filters = new HideOnScreen\Filters();

        if ($filters->is_hidden($list_screen)) {
            return false;
        }

        foreach ($list_screen->get_columns() as $column) {
            $comparison = (new ComparisonFactory())->create($column);

            if ( ! $comparison) {
                continue;
            }

            $setting = $column->get_setting('filter');

            if ($setting instanceof Settings && $setting->is_active()) {
                return true;
            }
        }

        return false;
    }

}