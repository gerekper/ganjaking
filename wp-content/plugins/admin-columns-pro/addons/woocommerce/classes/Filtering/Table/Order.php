<?php

declare(strict_types=1);

namespace ACA\WC\Filtering\Table;

use AC\Registerable;
use ACP\Filtering\View\FilterContainer;

class Order implements Registerable
{

    private $column_name;

    public function __construct(string $column_name)
    {
        $this->column_name = $column_name;
    }

    public function register(): void
    {
        add_action('woocommerce_order_list_table_restrict_manage_orders', [$this, 'render'], 11);
    }

    public function render(): void
    {
        echo new FilterContainer($this->column_name);
    }

}