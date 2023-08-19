<?php

namespace ACP\Sorting\Model\User;

use ACP\Search\Query\Bindings;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class UserField extends AbstractModel implements QueryBindings
{

    protected $field;

    public function __construct(string $field)
    {
        parent::__construct();

        $this->field = $field;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create(
                "$wpdb->users.$this->field",
                (string)$order
            )
        );
    }

}