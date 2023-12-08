<?php

namespace ACP\Search\Helper\MetaQuery;

use ACP\Search\Value;

class Comparison
{

    protected $key;

    protected $operator;

    protected $value;

    public function __construct(string $key, string $operator, Value $value)
    {
        $this->key = $key;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function __invoke(): array
    {
        switch ($this->value->get_type()) {
            case Value::INT:
                $type = 'NUMERIC';

                break;
            case Value::DECIMAL:
                $type = 'DECIMAL';

                break;
            case Value::DATE:
                $type = 'DATE';

                break;
            default:
                $type = 'CHAR';
        }

        return [
            'key'     => $this->key,
            'value'   => $this->value->get_value(),
            'compare' => $this->operator,
            'type'    => $type,
        ];
    }

}