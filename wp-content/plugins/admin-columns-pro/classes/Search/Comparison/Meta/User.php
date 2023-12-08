<?php

namespace ACP\Search\Comparison\Meta;

use AC\Meta\Query;
use ACP\Helper\Select;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\UserValuesTrait;
use ACP\Search\Value;

class User extends Meta
    implements SearchableValues
{

    use UserValuesTrait;
    
    private $query;

    public function __construct(string $meta_key, Query $query = null)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::CURRENT_USER,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, Value::INT);

        $this->query = $query;
    }

}