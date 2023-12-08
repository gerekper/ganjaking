<?php

namespace ACA\WC\Search\ShopOrder;

use ACA\WC\Search\ShopOrder\Address\Country;
use ACP;

class AddressFactory
{

    /**
     * @param string $address_property
     * @param string $meta_key
     *
     * @return ACP\Search\Comparison
     */
    public function create($address_property, $meta_key)
    {
        switch ($address_property) {
            case 'country' :
                return new Country($meta_key);
            default :
                return new ACP\Search\Comparison\Meta\Text($meta_key);
        }
    }

}