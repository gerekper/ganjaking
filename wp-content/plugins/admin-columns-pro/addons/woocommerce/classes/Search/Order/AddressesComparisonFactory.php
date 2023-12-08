<?php

declare(strict_types=1);

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACA\WC\Type\AddressType;
use ACP\Search\Comparison;

class AddressesComparisonFactory
{

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        $this->address_type = $address_type;
    }

    public function create(string $property): Comparison
    {
        switch ($property) {
            case 'address_1':
                return new Search\Order\Addresses('address_1', $this->address_type);
            case 'address_2':
                return new Search\Order\Addresses('address_2', $this->address_type);
            case 'city':
                return new Search\Order\Addresses('city', $this->address_type);
            case 'company':
                return new Search\Order\Addresses('company', $this->address_type);
            case 'country':
                return new Search\Order\Address\Country($this->address_type);
            case 'email':
                return new Search\Order\Addresses('email', $this->address_type);
            case 'first_name':
                return new Search\Order\Addresses('first_name', $this->address_type);
            case 'last_name':
                return new Search\Order\Addresses('last_name', $this->address_type);
            case 'full_name':
                return new Search\Order\Address\FullName($this->address_type);
            case 'postcode':
                return new Search\Order\Addresses('postcode', $this->address_type);
            case 'phone':
                return new Search\Order\Addresses('phone', $this->address_type);
            case 'state':
                return new Search\Order\Addresses('state', $this->address_type);
            default:
                return new Search\Order\Address\FullAddress($this->address_type);
        }
    }
}