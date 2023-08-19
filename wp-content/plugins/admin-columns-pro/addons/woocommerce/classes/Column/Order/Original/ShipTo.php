<?php

namespace ACA\WC\Column\Order\Original;

use AC;
use ACA\WC\Search;
use ACA\WC\Type\AddressType;
use ACP;

class ShipTo extends AC\Column implements ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('shipping_address')
             ->set_original(true);
    }

    public function search()
    {
        return new Search\Order\Address\FullAddress(new AddressType('shipping'));
    }

}