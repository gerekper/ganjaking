<?php

namespace ACA\WC\Sorting\Comment;

use ACP;
use ACP\Search\Query\Bindings;
use ACP\Sorting\Type\Order;

class Rating extends ACP\Sorting\AbstractModel implements ACP\Sorting\Model\QueryBindings
{

    private $meta_key;

    public function __construct(string $meta_key)
    {
        parent::__construct();

        $this->meta_key = $meta_key;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        $bindings = new Bindings();

        $bindings->meta_query(
            [

                'key'     => $this->meta_key,
                'type'    => $this->data_type->get_value(),
                'value'   => '',
                'compare' => '!=',
            ]
        );

        return $bindings;
    }

}