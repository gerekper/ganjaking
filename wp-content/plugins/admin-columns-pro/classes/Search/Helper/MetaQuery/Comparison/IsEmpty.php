<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\MetaQuery;
use ACP\Search\Value;

class IsEmpty extends MetaQuery\Comparison
{

    public function __construct(string $key, Value $value)
    {
        if ($value->get_value()) {
            $value = new Value(
                null,
                $value->get_type()
            );
        }

        parent::__construct($key, 'NOT EXISTS', $value);
    }

    public function __invoke(): array
    {
        return [
            'relation' => 'OR',
            [
                'key'     => $this->key,
                'compare' => $this->operator,
            ],
            [
                'key'   => $this->key,
                'value' => $this->value->get_value(),
            ],
        ];
    }

}