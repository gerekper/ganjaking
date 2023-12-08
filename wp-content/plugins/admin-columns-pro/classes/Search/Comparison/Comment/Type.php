<?php

namespace ACP\Search\Comparison\Comment;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Type extends Field
    implements Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators);
    }

    protected function get_field(): string
    {
        return 'comment_type';
    }

    public function get_values(): Options
    {
        $options = [];

        foreach ($this->get_comment_types() as $type) {
            if (null === $type) {
                continue;
            }

            $options[] = new AC\Helper\Select\Option($type, $type);
        }

        return new AC\Helper\Select\Options($options);
    }

    private function get_comment_types(): array
    {
        global $wpdb;

        return $wpdb->get_col("SELECT DISTINCT( comment_type ) FROM $wpdb->comments WHERE comment_type != ''");
    }

}