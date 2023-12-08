<?php

namespace ACA\WC\Search\ShopOrder;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class CouponsUsed extends Comparison
{

    public function __construct()
    {
        $operators = new Operators(
            [
                Operators::EQ,
                Operators::CONTAINS,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]
        );

        parent::__construct($operators, Value::STRING);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        switch ($operator) {
            case Operators::IS_EMPTY:
                $sub_operator = 'NOT_IN';
                break;
            default:
                $sub_operator = 'IN';
        }

        $bindings = new Bindings();

        $sql = $this->get_order_with_coupon_sub_query($operator, $value);

        $bindings->where(sprintf("$wpdb->posts.ID $sub_operator ( %s )", $sql));

        return $bindings;
    }

    protected function get_order_with_coupon_sub_query(string $operator, Value $coupon): string
    {
        global $wpdb;

        switch (true) {
            case $operator === Operators::CONTAINS:
            case $operator === Operators::EQ:
                $where = ' AND ' . ComparisonFactory::create('oi.order_item_name', $operator, $coupon)->prepare();
                break;
            default:
                $where = '';
        }

        return "SELECT distinct(pp.ID) AS ID
					FROM {$wpdb->prefix}woocommerce_order_items AS oi
					JOIN $wpdb->posts AS pp ON pp.ID = oi.order_id
					WHERE oi.order_item_type = 'coupon' " . $where;
    }

}