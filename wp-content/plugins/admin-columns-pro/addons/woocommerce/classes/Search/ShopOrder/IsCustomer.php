<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class IsCustomer extends Comparison\Meta
    implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators(
            [
                Operators::EQ,
            ]
        );

        parent::__construct($operators, '_customer_user');
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            0 => __('Guest', 'woocommerce'),
            1 => __('Customer', 'woocommerce'),
        ]);
    }

}