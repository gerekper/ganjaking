<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use AC;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings\QueryArguments;
use ACP\Search\Value;

class IsCustomer extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'customer_id',
                    'value'   => 0,
                    'compare' => $value->get_value() === '0' ? '=' : '!=',
                ],
            ],
        ]);

        return $bindings;
    }

    public function get_values()
    {
        return AC\Helper\Select\Options::create_from_array([
            '0' => __('Guest', 'woocommerce'),
            '1' => __('Customer', 'woocommerce'),
        ]);
    }

}