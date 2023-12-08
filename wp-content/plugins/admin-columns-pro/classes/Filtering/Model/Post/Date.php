<?php

namespace ACP\Filtering\Model\Post;

use AC\Column;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

/**
 * @deprecated NEWVERSION
 */
class Date extends Comparison
{

    public function __construct(Column $column)
    {
        parent::__construct(new Operators([Operators::EQ]));
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return new Bindings();
    }

}