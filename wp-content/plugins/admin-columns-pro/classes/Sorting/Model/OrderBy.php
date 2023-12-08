<?php

namespace ACP\Sorting\Model;

use ACP\Query\Bindings;
use ACP\Sorting\Type\Order;

class OrderBy implements QueryBindings
{

    protected $orderby;

    public function __construct(string $orderby)
    {
        $this->orderby = $orderby;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new Bindings\QueryArguments())->query_arguments(
            [
                'orderby' => $this->orderby,
            ]
        );
    }

}