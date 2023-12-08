<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Value;

class Users extends User
{

    use MultiMapTrait;

    protected function get_subquery(string $operator, Value $value): string
    {
        $operator = $this->map_operator($operator);
        $value = $this->map_value($value, $operator);

        $where = ComparisonFactory::create($this->column, $operator, $value);

        return "SELECT ID FROM $this->table WHERE " . $where->prepare();
    }

}