<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;

class Title extends PostField
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
            Operators::EQ,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ], false);

        parent::__construct($operators);
    }

    protected function get_field(): string
    {
        return 'post_title';
    }

}