<?php

namespace ACA\WC\Search\Order;

use AC;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Customer extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $compare = '=';
        $customer_id = $value->get_value();

        switch ($operator) {
            case Operators::IS_EMPTY:
                $customer_id = 0;
                break;
            case Operators::NOT_IS_EMPTY:
                $compare = '!=';
                $customer_id = 0;
        }

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'customer_id',
                    'value'   => $customer_id,
                    'compare' => $compare,
                ],
            ],
        ]);

        return $bindings;
    }

    public function get_values($search, $paged)
    {
        $entities = new ACP\Helper\Select\Entities\User(compact('search', 'paged'));

        return new AC\Helper\Select\Options\Paginated(
            $entities,
            new ACP\Helper\Select\Group\UserRole(
                new ACP\Helper\Select\Formatter\UserName($entities)
            )
        );
    }

}