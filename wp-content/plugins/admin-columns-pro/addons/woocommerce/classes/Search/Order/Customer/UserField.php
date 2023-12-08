<?php

namespace ACA\WC\Search\Order\Customer;

use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class UserField extends ACP\Search\Comparison
{

    private $field;

    public function __construct(string $field, Operators $operators = null, string $value_type = null)
    {
        $operators = $operators
            ?: new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
                Operators::EQ,
                Operators::NEQ,
            ], false);

        parent::__construct(
            $operators,
            $value_type
        );

        $this->field = $field;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where = ComparisonFactory::create(
            "u.$this->field",
            $operator,
            $value
        );

        $subquery = $wpdb->prepare("SELECT u.ID FROM $wpdb->users AS u WHERE {$where()}");
        $alias = $bindings->get_unique_alias('usq');
        $order_table = $wpdb->prefix . Orders::TABLE;
        $bindings->join("JOIN($subquery) as $alias on $order_table.customer_id = $alias.ID");

        return $bindings;
    }

}