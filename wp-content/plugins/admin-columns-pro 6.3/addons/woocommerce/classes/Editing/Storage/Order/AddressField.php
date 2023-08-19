<?php

declare(strict_types=1);

namespace ACA\WC\Editing\Storage\Order;

use ACA\WC\Type\AddressType;
use ACP\Editing\Storage;
use LogicException;

class AddressField implements Storage
{

    private $address_type;

    private $address_property;

    public function __construct(AddressType $address_type, string $address_property)
    {
        $this->address_type = $address_type;
        $this->address_property = $address_property;
    }

    private function get_retrieve_method(): string
    {
        return 'get_' . $this->address_type . '_' . $this->address_property;
    }

    private function get_update_method(): string
    {
        return 'set_' . $this->address_type . '_' . $this->address_property;
    }

    public function get(int $id)
    {
        $order = wc_get_order($id);
        $method = $this->get_retrieve_method();

        if ( ! method_exists($order, $method)) {
            throw new LogicException('Method ' . $method . ' does not exist on Order');
        }

        return $order->$method();
    }

    public function update(int $id, $data): bool
    {
        $order = wc_get_order($id);
        $method = $this->get_update_method();

        if ( ! method_exists($order, $method)) {
            throw new LogicException('Method ' . $method . ' does not exist on Order');
        }

        $order->$method($data);
        $order->save();

        return true;
    }

}