<?php

namespace ACA\BP\Search\Profile;

use AC\Helper\Select\Options;
use ACA\BP\Helper\Select;
use ACA\BP\Search;
use ACP\Query\Bindings;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;

class Date extends Search\Profile implements RemoteValues
{

    public function __construct($meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::EQ_MONTH,
            Operators::EQ_YEAR,
            Operators::FUTURE,
            Operators::PAST,
            Operators::BETWEEN,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ], false);

        parent::__construct($operators, $meta_key, Value::DATE, new Labels\Date());
    }

    public function format_label(string $value): string
    {
        $date = DateTime::createFromFormat('Ym', $value);

        return $date ? $date->format('F Y') : $value;
    }

    public function get_values(): Options
    {
        global $wpdb, $bp;

        $table = (string)$bp->profile->table_name_data;

        $sql = $wpdb->prepare(
            "SELECT DATE_FORMAT(value,'%Y%m')
            FROM $table
            WHERE field_id = %d
            ",
            $this->field
        );

        $options = [];

        foreach ($wpdb->get_col($sql) as $value) {
            $date = DateTime::createFromFormat('Ym', $value);

            if ( ! $date) {
                continue;
            }

            $options[$date->format('Ym')] = $date->format('F Y');
        }

        krsort($options);

        return Options::create_from_array($options);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        if (Operators::EQ === $operator) {
            $value = new Value(
                [
                    $value->get_value() . ' 00:00:00',
                    $value->get_value() . ' 23:59:59',
                ],
                Value::DATE
            );
            $operator = Operators::BETWEEN;
        }

        return parent::create_query_bindings($operator, $value);
    }

}