<?php

namespace ACA\WC\Search\ShopOrder;

use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Currency extends Comparison\Meta
    implements Comparison\RemoteValues
{

    public function __construct()
    {
        $operators = new Operators(
            [
                Operators::EQ,
            ]
        );

        parent::__construct($operators, '_order_currency');
    }

    public function format_label(string $value): string
    {
        return $value;
    }

    public function get_values(): Options
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT DISTINCT(meta_value) FROM $wpdb->postmeta WHERE meta_key = %s",
            $this->get_meta_key()
        );
        $entities = $wpdb->get_col($sql);

        $options = array_combine($entities, $entities);

        return Options::create_from_array($options);
    }

}