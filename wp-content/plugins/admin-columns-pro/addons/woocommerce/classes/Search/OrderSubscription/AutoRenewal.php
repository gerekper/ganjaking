<?php

namespace ACA\WC\Search\OrderSubscription;

use AC\Helper\Select\Options;
use ACA\GravityForms\Search\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class AutoRenewal extends Comparison implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    public function get_values()
    {
        return Options::create_from_array([
            'true'  => __('Manual Renewal', 'woocommerce-subscriptions'),
            'false' => __('Auto Renewal', 'codepress-admin-columns'),
        ]);
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new Bindings();
        $bindings->meta_query([
            'key'   => '_requires_manual_renewal',
            'value' => $value->get_value(),
        ]);

        return $bindings;
    }

}