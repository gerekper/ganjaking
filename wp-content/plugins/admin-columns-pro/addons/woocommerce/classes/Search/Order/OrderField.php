<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class OrderField extends ACP\Search\Comparison
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
        $column = sprintf('%s.%s', $wpdb->prefix . Orders::TABLE, $this->field);
        $bindings->where(ACP\Search\Helper\Sql\ComparisonFactory::create($column, $operator, $value)());

        return $bindings;
    }

}