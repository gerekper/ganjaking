<?php

namespace ACA\Types\Search;

use ACP;
use ACP\Search\Operators;

class Skype extends ACP\Search\Comparison\Meta
{

    public function __construct(string $meta_key, string $value_type = null)
    {
        $operators = new Operators([
            Operators::CONTAINS,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, $value_type);
    }

}