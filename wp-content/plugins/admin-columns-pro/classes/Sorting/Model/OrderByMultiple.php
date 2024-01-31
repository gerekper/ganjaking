<?php

namespace ACP\Sorting\Model;

use ACP\Query\Bindings;
use ACP\Sorting\Type\Order;

class OrderByMultiple implements QueryBindings
{

    private $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $orderby = [];

        foreach ($this->columns as $column) {
            $orderby[$column] = (string)$order;
        }

        return (new Bindings\QueryArguments())->query_arguments(
            [
                'orderby' => $orderby,
            ]
        );
    }

}