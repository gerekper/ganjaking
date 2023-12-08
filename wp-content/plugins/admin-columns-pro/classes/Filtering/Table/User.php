<?php

declare(strict_types=1);

namespace ACP\Filtering\Table;

use AC\Registerable;
use ACP\Filtering\View\FilterContainer;

class User implements Registerable
{

    private $column_name;

    public function __construct(string $column_name)
    {
        $this->column_name = $column_name;
    }

    public function register(): void
    {
        add_action('restrict_manage_users', [$this, 'render'], 1);
    }

    public function render($which): void
    {
        if ('top' === $which) {
            echo new FilterContainer($this->column_name);
        }
    }

}