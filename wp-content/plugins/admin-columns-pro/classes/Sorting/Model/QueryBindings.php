<?php

declare(strict_types=1);

namespace ACP\Sorting\Model;

use ACP\Query\Bindings;
use ACP\Sorting\Type\Order;

interface QueryBindings
{

    public function create_query_bindings(Order $order): Bindings;
}