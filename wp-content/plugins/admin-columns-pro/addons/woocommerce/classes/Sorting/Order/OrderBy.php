<?php

namespace ACA\WC\Sorting\Order;

use ACP\Search\Query\Bindings;
use ACP\Search\Query\Bindings\QueryArguments;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Type\Order;

class OrderBy extends AbstractModel implements QueryBindings
{

    private $orderby_key;

    public function __construct(string $orderby_key)
    {
        parent::__construct();

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