<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;

class OrderByNonUnique implements QueryBindings
{

    private $order_by;

    public function __construct(string $orderby)
    {
        $this->order_by = $orderby;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new Bindings\QueryArguments())->query_arguments(
            [
                'orderby' => [
                    $this->order_by => (string)$order,
                    'comment_ID'    => (string)$order,
                ],
            ]
        );
    }

}