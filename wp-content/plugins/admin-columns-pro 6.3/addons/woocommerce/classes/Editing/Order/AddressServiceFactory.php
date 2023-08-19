<?php

namespace ACA\WC\Editing\Order;

use ACA\WC\Editing;
use ACA\WC\Type\AddressType;
use ACP;

final class AddressServiceFactory
{

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        $this->address_type = $address_type;
    }

    public function create(string $property): ?ACP\Editing\Service
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
                return new ACP\Editing\Service\Basic(
                    $this->create_view($property),
                    new Editing\Storage\Order\AddressField($this->address_type, $property)
                );

            case 'full_name':
            default:
                return null;
        }
    }

    public function create_view(string $property): ACP\Editing\View
    {
        switch ($property) {
            case 'country':
                return new ACP\Editing\View\Select(WC()->countries->get_countries());
            case 'email':
                return new ACP\Editing\View\Email();
            default:
                return new ACP\Editing\View\Text();
        }
    }

}