<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;

class Currency extends OrderField implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            Orders::CURRENCY,
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    public function get_values(): Options
    {
        global $wpdb;

        $table = $wpdb->prefix . Orders::TABLE;
        $field = Orders::CURRENCY;

        $sql = "SELECT DISTINCT($field) FROM $table";
        $entities = $wpdb->get_col($sql);

        $options = array_combine($entities, $entities);

        return Options::create_from_array($options);
    }

}