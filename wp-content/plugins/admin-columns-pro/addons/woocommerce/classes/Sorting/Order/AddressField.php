<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACA\WC\Type\AddressType;
use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;
use InvalidArgumentException;

class AddressField implements QueryBindings
{

    private $address_field;

    private $address_type;

    public function __construct(string $address_field, AddressType $address_type)
    {
        $this->address_field = $address_field;
        $this->address_type = $address_type;

        $this->validate();
    }

    private function validate(): void
    {
        if ('' === $this->address_field) {
            throw new InvalidArgumentException('Invalid address field');
        }
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('wcs_aaf');

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_addresses = $wpdb->prefix . 'wc_order_addresses';

        $bindings->join(
            $wpdb->prepare(
                "\nLEFT JOIN $table_addresses AS $alias ON $alias.order_id = $table_orders.id 
                    AND $alias.address_type = %s",
                (string)$this->address_type
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.$this->address_field",
                (string)$order
            )
        );

        return $bindings;
    }

}