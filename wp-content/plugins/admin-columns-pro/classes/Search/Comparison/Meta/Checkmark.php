<?php

namespace ACP\Search\Comparison\Meta;

use AC\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkmark extends Meta
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

    public function get_values(): Options
    {
        return Options::create_from_array([
            '1' => __('True', 'codepress-admin-columns'),
            '0' => __('False', 'codepress-admin-columns'),
        ]);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        switch ($value->get_value()) {
            case '1' :
                return [
                    'key'     => $this->meta_key,
                    'value'   => ['0', 'no', 'false', 'off', ''],
                    'compare' => 'NOT IN',
                ];
            case '0' :
                return [
                    'relation' => 'OR',
                    [
                        'key'     => $this->meta_key,
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key'   => $this->meta_key,
                        'value' => ['0', 'no', 'false', 'off', ''],
                    ],
                ];
            default:
                return parent::get_meta_query($operator, $value);
        }
    }

}