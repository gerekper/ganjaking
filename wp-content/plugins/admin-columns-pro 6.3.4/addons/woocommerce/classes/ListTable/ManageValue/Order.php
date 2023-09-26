<?php

declare(strict_types=1);

namespace ACA\WC\ListTable\ManageValue;

use AC\ColumnRepository;
use AC\Table\ManageValue;
use Automattic;
use DomainException;
use WC_Order;

class Order extends ManageValue
{

    private $order_type;

    public function __construct(string $order_type, ColumnRepository $column_repository)
    {
        parent::__construct($column_repository);

        $this->order_type = $order_type;
    }

    public function register(): void
    {
        $action = sprintf('woocommerce_%s_list_table_custom_column', $this->order_type);

        if (did_action($action)) {
            throw new DomainException(sprintf("Method should be called before the %s action.", $action));
        }

        add_action($action, [$this, 'render_value'], 100, 2);
    }

    public function render_value($column_name, WC_Order $order): void
    {
        echo $this->render_cell((string)$column_name, $order->get_id());
    }

}