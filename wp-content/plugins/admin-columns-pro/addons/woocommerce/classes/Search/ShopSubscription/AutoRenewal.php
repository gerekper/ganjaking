<?php

namespace ACA\WC\Search\ShopSubscription;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class AutoRenewal extends Comparison\Meta
    implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([Operators::EQ]);

        parent::__construct($operators, '_requires_manual_renewal');
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            'false' => __('True'),
            'true'  => __('False'),
        ]);
    }

}