<?php

declare(strict_types=1);

namespace ACA\WC\ColumnValue;

use ACA\WC\Type\AddressType;

class OrderAddress
{

    private $type;

    private $property;

    private $empty_char;

    public function __construct(AddressType $type, string $property, string $empty_char = null)
    {
        if (null === $empty_char) {
            $empty_char = '&ndash;';
        }

        $this->empty_char = $empty_char;
        $this->property = $property;
        $this->type = $type;
    }

    public function render(int $id): string
    {
        $order = wc_get_order($id);

        $method = $this->get_address_method();

        if ( ! method_exists($order, $method)) {
            return $this->empty_char;
        }

        $value = $order->$method();

        switch ($this->property) {
            case 'country' :
                return WC()->countries->get_countries()[$value] ?? $this->empty_char;
            default:
                return $value ?: $this->empty_char;
        }
    }

    private function get_address_method(): string
    {
        $mapping = [
            'address_1'  => 'get_%s_address_1',
            'address_2'  => 'get_%s_address_2',
            'city'       => 'get_%s_city',
            'company'    => 'get_%s_company',
            'country'    => 'get_%s_country',
            'first_name' => 'get_%s_first_name',
            'last_name'  => 'get_%s_last_name',
            'full_name'  => 'get_formatted_%s_full_name',
            'postcode'   => 'get_%s_postcode',
            'state'      => 'get_%s_state',
            'email'      => 'get_%s_email',
            'phone'      => 'get_%s_phone',
        ];

        $method = $mapping[$this->property] ?? 'get_formatted_%s_address';

        return sprintf($method, $this->type);
    }

}