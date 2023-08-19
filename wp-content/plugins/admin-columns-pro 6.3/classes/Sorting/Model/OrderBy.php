<?php

namespace ACP\Sorting\Model;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\Order;

class OrderBy extends AbstractModel implements QueryBindings
{

    protected $orderby;

    public function __construct(string $orderby)
    {
        parent::__construct();

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