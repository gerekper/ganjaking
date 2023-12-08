<?php

namespace ACA\EC\Search\Event;

use AC;
use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Recurring extends Comparison implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $not_recur = 's:5:"rules";a:0:{}s:10:"exclusions";a:0:{}';

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('ecrec');
        $bindings->join(
            " JOIN {$wpdb->postmeta} as {$alias} ON {$wpdb->posts}.ID = {$alias}.post_id AND {$alias}.meta_key = '_EventRecurrence'"
        );

        $join_type = 'yes' === $value->get_value()
            ? 'NOT LIKE'
            : 'LIKE';

        $where = "{$alias}.meta_value {$join_type} '%$not_recur%'";

        if ('yes' !== $value->get_value()) {
            $where .= " OR ${alias}.meta_value = ''";
        }

        $bindings->where('(' . $where . ')');

        return $bindings;
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            -1 => __('True', 'codepress-admin-columns'),
            0  => __('False', 'codepress-admin-columns'),
        ]);
    }

}