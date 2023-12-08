<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;

class PostName extends PostField
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
            Operators::EQ,
            Operators::BEGINS_WITH,
            Operators::ENDS_WITH,
        ], false);

        parent::__construct($operators);
    }

    protected function get_field(): string
    {
        return 'post_name';
    }

}