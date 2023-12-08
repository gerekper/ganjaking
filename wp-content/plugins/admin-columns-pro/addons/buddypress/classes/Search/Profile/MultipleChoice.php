<?php

namespace ACA\BP\Search\Profile;

use AC;
use AC\Helper\Select\Options;
use ACA\BP\Helper\Select;
use ACA\BP\Search;
use ACP\Query\Bindings;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class MultipleChoice extends Search\Profile
    implements Values
{

    /** @var array */
    private $options;

    public function __construct($meta_key, $options)
    {
        $this->options = $options;

        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ], false);

        parent::__construct($operators, $meta_key, Value::STRING);
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->options);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        switch ($operator) {
            case Operators::EQ:
                $value = new Value(
                    serialize($value->get_value()),
                    Value::STRING
                );

                return parent::create_query_bindings(Operators::CONTAINS, $value);
            case Operators::NEQ:
                $value = new Value(
                    serialize($value->get_value()),
                    Value::STRING
                );

                return parent::create_query_bindings(Operators::NOT_CONTAINS, $value);
            default:
                return parent::create_query_bindings($operator, $value);
        }
    }

}