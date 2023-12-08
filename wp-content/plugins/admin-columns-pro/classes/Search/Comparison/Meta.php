<?php

namespace ACP\Search\Comparison;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Meta extends Comparison
{

    protected $meta_key;

    public function __construct(
        Operators $operators,
        string $meta_key,
        string $value_type = null,
        Labels $labels = null
    ) {
        parent::__construct($operators, $value_type, $labels);

        $this->meta_key = $meta_key;
    }

    protected function get_meta_key(): string
    {
        return $this->meta_key;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings();
        $bindings->meta_query(
            $this->get_meta_query($operator, $value)
        );

        return $bindings;
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $comparison = MetaQuery\ComparisonFactory::create(
            $this->meta_key,
            $operator,
            $value
        );

        return $comparison();
    }

}