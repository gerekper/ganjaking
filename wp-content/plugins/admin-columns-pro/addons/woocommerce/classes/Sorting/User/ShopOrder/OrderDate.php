<?php

namespace ACA\WC\Sorting\User\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\Order;

abstract class OrderDate implements QueryBindings
{

    use SqlTrait;

    private $status;

    public function __construct(array $status = ['wc-completed'])
    {
        $this->status = $status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where_status = $this->status
            ? sprintf(" AND acsort_orders.post_status IN ( %s )", $this->esc_sql_array($this->status))
            : '';
        $bindings->join(
            " 
            LEFT JOIN $wpdb->postmeta AS acsort_postmeta 
                ON $wpdb->users.ID = acsort_postmeta.meta_value
                AND acsort_postmeta.meta_key = '_customer_user'
            LEFT JOIN $wpdb->posts AS acsort_orders
                ON acsort_orders.ID = acsort_postmeta.post_id
                AND acsort_orders.post_type = 'shop_order'
                $where_status
            LEFT JOIN $wpdb->postmeta AS acsort_order_postmeta
                ON acsort_orders.ID = acsort_order_postmeta.post_id
                AND acsort_order_postmeta.meta_key = '_completed_date'
            "
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by($this->get_order_by($order));

        return $bindings;
    }

    abstract protected function get_order_by(Order $order): string;

}