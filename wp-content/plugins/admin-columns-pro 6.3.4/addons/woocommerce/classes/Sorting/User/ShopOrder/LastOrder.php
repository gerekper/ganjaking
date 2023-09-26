<?php

namespace ACA\WC\Sorting\User\ShopOrder;

use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class LastOrder extends OrderDate
{

    protected function get_order_by(Order $order): string
    {
        return SqlOrderByFactory::create_with_computation(
            new ComputationType(ComputationType::MAX),
            'acsort_order_postmeta.meta_value',
            (string)$order
        );
    }

}