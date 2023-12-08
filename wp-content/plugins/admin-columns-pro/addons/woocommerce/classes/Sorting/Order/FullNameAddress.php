<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACA\WC\Type\AddressType;
use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class FullNameAddress implements QueryBindings
{

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        $this->address_type = $address_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_addresses = $wpdb->prefix . 'wc_order_addresses';

        $alias = $bindings->get_unique_alias('wcs_fna');

        $bindings->join(
            $wpdb->prepare(
                "\nLEFT JOIN $table_addresses AS $alias ON $alias.order_id = $table_orders.id
				AND $alias.address_type = %s
			",
                (string)$this->address_type
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                [
                    "$alias.first_name",
                    "$alias.last_name",
                ],
                (string)$order
            )
        );

        return $bindings;
    }

}