<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class IsReply extends Comparison
    implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings\Comment();

        switch ($value->get_value()) {
            case '1':
                $where = "{$wpdb->comments}.comment_parent != 0";
                break;
            default:
                $where = "{$wpdb->comments}.comment_parent = 0";
        }

        return $bindings->where($where);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            '1' => __('True', 'codepress-admin-columns'),
            '0' => __('False', 'codepress-admin-columns'),
        ]);
    }

}