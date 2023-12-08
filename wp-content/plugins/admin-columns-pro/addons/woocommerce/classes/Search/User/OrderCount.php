<?php

namespace ACA\WC\Search\User;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class OrderCount extends Comparison
{

    /**
     * @var array
     */
    protected $status;

    public function __construct(array $status = [])
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::IS_EMPTY,
            Operators::BETWEEN,
        ]);

        $this->status = $status;

        parent::__construct($operators, Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where = $this->status
            ? sprintf("AND status IN ( '%s' )", implode("','", array_map('esc_sql', $this->status)))
            : "AND `status` NOT IN( 'trash' )";

        if (Operators::IS_EMPTY === $operator) {
            return $bindings->where(
                "$wpdb->users.ID NOT IN( SELECT DISTINCT(customer_id) FROM {$wpdb->prefix}wc_orders WHERE type='shop_order' $where)"
            );
        }

        $comparison = ComparisonFactory::create('order_count', $operator, $value);
        $having = $comparison();

        $alias = $bindings->get_unique_alias('sq');
        $table = $wpdb->prefix . 'wc_orders';

        $subquery = "SELECT customer_id,COUNT(id) as order_count FROM $table WHERE type = 'shop_order' $where GROUP BY customer_id HAVING $having";

        $bindings->join("JOIN( $subquery ) AS $alias ON $wpdb->users.ID = $alias.customer_id");

        return $bindings;
    }

}