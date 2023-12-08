<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use AC;
use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class IsCustomer extends OrderField implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            Orders::CUSTOMER_ID,
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return parent::create_query_bindings($value->get_value() === '0' ? '=' : '!=', new Value(0));
    }

    public function get_values(): AC\Helper\Select\Options
    {
        return AC\Helper\Select\Options::create_from_array([
            '0' => __('Guest', 'woocommerce'),
            '1' => __('Customer', 'woocommerce'),
        ]);
    }

}