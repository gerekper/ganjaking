<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class OperationalDataField extends ACP\Search\Comparison
{

    private $field;

    public function __construct(string $field, Operators $operators, string $value_type = null, Labels $labels = null)
    {
        parent::__construct($operators, $value_type, $labels);

        $this->field = $field;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings\QueryArguments();

        $table_orders = $wpdb->prefix . Orders::TABLE;
        $table = $wpdb->prefix . OrderOperationalData::TABLE;

        $alias = $bindings->get_unique_alias('operational_data');

        $bindings->join("JOIN $table AS $alias ON $table_orders.id = $alias.order_id");
        $bindings->where(
            ComparisonFactory::create("$alias.$this->field", $operator, $value)()
        );

        return $bindings;
    }

}