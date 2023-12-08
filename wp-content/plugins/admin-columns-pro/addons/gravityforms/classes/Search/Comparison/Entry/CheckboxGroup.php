<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use AC\Helper\Select\Options;
use ACA\GravityForms\Field\Type\Checkbox;
use ACA\GravityForms\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class CheckboxGroup extends Search\Comparison\Entry implements ACP\Search\Comparison\Values
{

    /**
     * @var array
     */
    private $choices;

    /**
     * @var Checkbox[]
     */
    private $sub_fields;

    public function __construct(string $field, array $choices, array $sub_fields)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
        ]);

        parent::__construct($field, $operators, Value::STRING);

        $this->choices = $choices;
        $this->sub_fields = $sub_fields;
    }

    private function get_field_id_for_value(Value $value)
    {
        foreach ($this->sub_fields as $field_id => $sub_field) {
            if ($value->get_value() === $sub_field->get_value()) {
                return $field_id;
            }
        }

        return null;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $this->meta_key = $this->get_field_id_for_value($value);

        return parent::create_query_bindings($operator === Operators::NEQ ? Operators::IS_EMPTY : $operator, $value);
    }

    public function get_values(): Options
    {
        return Options::create_from_array($this->choices);
    }

}