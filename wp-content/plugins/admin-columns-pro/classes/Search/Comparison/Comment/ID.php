<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Search\Operators;
use ACP\Search\Value;

class ID extends Field
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::LT,
            Operators::BETWEEN,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function get_field(): string
    {
        return 'comment_ID';
    }

}