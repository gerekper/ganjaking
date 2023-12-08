<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP;
use ACP\Search\Operators;
use ACP\Search\UserValuesTrait;
use ACP\Search\Value;

class User extends TableStorage implements ACP\Search\Comparison\SearchableValues
{

    use UserValuesTrait;

    /**
     * @var array
     */
    protected $query_args;

    public function __construct(
        Operators $operators,
        string $table,
        string $column,
        array $query_args = [],
        string $value_type = Value::INT
    ) {
        $this->query_args = $query_args;

        parent::__construct($operators, $table, $column, $value_type);
    }

}