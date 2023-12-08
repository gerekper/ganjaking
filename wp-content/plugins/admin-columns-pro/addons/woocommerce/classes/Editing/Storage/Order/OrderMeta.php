<?php

declare(strict_types=1);

namespace ACA\WC\Editing\Storage\Order;

use ACP\Editing\Storage;

class OrderMeta implements Storage
{

    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function get(int $id)
    {
        $order = wc_get_order($id);

        return $order ? $order->get_meta($this->key) : false;
    }

    public function update(int $id, $data): bool
    {
        $order = wc_get_order($id);

        $order->update_meta_data($this->key, $data);

        $order->save();

        return true;
    }

}