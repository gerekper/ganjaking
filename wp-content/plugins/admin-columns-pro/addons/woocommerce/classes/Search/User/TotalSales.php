<?php

namespace ACA\WC\Search\User;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class TotalSales extends Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::GT,
            Operators::LT,
            Operators::BETWEEN,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $having = ComparisonFactory::create('total', $operator, $value);
        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "AND wco.status IN( 'wc-" . implode("','wc-", $statuses) . "' )";

        $sql = "
            SELECT wco.customer_id, SUM(total_amount) as total
            FROM {$wpdb->prefix}wc_orders AS wco
            WHERE wco.customer_id IS NOT NULL {$statuses_sql}
            GROUP BY wco.customer_id
            HAVING {$having->prepare()}";
        $sq_alias = $bindings->get_unique_alias('sq');

        $bindings->join("JOIN ( {$sql} ) as $sq_alias on $sq_alias.customer_id = {$wpdb->users}.ID");

        return $bindings;
    }

}