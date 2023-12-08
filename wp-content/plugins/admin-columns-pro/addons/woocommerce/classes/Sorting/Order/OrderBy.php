<?php

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Query\Bindings\QueryArguments;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;

class OrderBy implements QueryBindings
{

    private $orderby_key;

    public function __construct(string $orderby_key)
    {
        $this->orderby_key = $orderby_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new QueryArguments())->query_arguments([
            'orderby' => $this->orderby_key,
            'order'   => (string)$order,
        ]);
    }

}