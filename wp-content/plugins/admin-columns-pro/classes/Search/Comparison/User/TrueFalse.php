<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Comparison;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class TrueFalse extends Comparison\Meta
{

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $labels = new Labels([
            Operators::NOT_IS_EMPTY => __('is true', 'codepress-admin-columns'),
            Operators::IS_EMPTY     => __('is false', 'codepress-admin-columns'),
        ]);

        parent::__construct(
            $operators,
            $meta_key,
            null,
            $labels
        );
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $value = new Value(
            ($operator === Operators::IS_EMPTY) ? 'false' : 'true',
            $value->get_type()
        );

        return parent::get_meta_query(Operators::EQ, $value);
    }

}