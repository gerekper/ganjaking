<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;

class CreatedVia extends OperationalDataField implements ACP\Search\Comparison\RemoteValues
{

    public function __construct()
    {
        parent::__construct(
            OrderOperationalData::CREATED_VIA,
            new Operators([
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    public function format_label(string $value): string
    {
        return $value;
    }

    public function get_values(): Options
    {
        global $wpdb;

        $table = $wpdb->prefix . OrderOperationalData::TABLE;
        $field = OrderOperationalData::CREATED_VIA;

        $sql = "SELECT DISTINCT($field) FROM $table";

        $values = $wpdb->get_col($sql);
        $options = array_combine($values, $values);

        return Options::create_from_array($options ? array_filter($options) : []);
    }

}