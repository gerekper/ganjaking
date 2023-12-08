<?php

namespace ACA\WC\ListTable;

use AC;
use Automattic;

class Orders implements AC\ListTable
{

    private $table;

    public function __construct(Automattic\WooCommerce\Internal\Admin\Orders\ListTable $list_table)
    {
        $this->table = $list_table;
    }

    public function get_total_items(): int
    {
        return $this->table->get_pagination_arg('total_items');
    }

    public function render_row($id): string
    {
        ob_start();

        $this->table->single_row(wc_get_order($id));

        return ob_get_clean();
    }

    public function get_column_value(string $column, $id): string
    {
        ob_start();

        $method = 'column_' . $column;

        if (method_exists($this->table, $method)) {
            call_user_func([$this->table, $method], wc_get_order($id));
        } else {
            $this->table->column_default(wc_get_order($id), $column);
        }

        return ob_get_clean();
    }

}