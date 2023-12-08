<?php

namespace ACA\EC\Search\Event;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class Featured extends Meta
    implements Values
{

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        if ('0' === $value->get_value()) {
            $operator = Operators::IS_EMPTY;
        }

        return parent::get_meta_query($operator, $value);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            __('False', 'codepress-admin-columns'),
            __('True', 'codepress-admin-columns'),
        ]);
    }

}