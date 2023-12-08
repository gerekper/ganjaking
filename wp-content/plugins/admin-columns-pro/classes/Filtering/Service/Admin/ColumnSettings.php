<?php

declare(strict_types=1);

namespace ACP\Filtering\Service\Admin;

use AC\Column;
use AC\Registerable;
use ACP\Filtering\Settings;
use ACP\Search\ComparisonFactory;

class ColumnSettings implements Registerable
{

    private $factory;

    public function __construct(ComparisonFactory $factory)
    {
        $this->factory = $factory;
    }

    public function register(): void
    {
        add_action('ac/column/settings', [$this, 'settings']);
    }

    public function settings(Column $column): void
    {
        if ( ! $this->factory->create($column)) {
            return;
        }

        if ($column->get_setting(Settings::NAME)) {
            return;
        }

        $column->add_setting(new Settings($column));
    }

}