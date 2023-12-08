<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACA\WC\Type\AddressType;

class AddressesFactory
{

    private $type;

    public function __construct(AddressType $type)
    {
        $this->type = $type;
    }

    public function create(string $property)
    {
        switch ($property) {
            case 'address_1':
            case 'address_2':
            case 'city':
            case 'company':
            case 'country':
            case 'email':
            case 'first_name':
            case 'last_name':
            case 'postcode':
            case 'phone':
            case 'state':
                return new AddressField($property, $this->type);
            case 'full_name':
                return new FullNameAddress($this->type);
            default:
                return null;
        }
    }

}