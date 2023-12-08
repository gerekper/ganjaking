<?php

namespace ACA\WC\Search\Order\Address;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACA\WC\Type\AddressType;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Country extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
{

    use Search\Order\OperatorMappingTrait;

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );

        $this->address_type = $address_type;
    }

    public function get_values(): Options
    {
        return Options::create_from_array(WC()->countries->get_countries());
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field' => $this->address_type . '_country',
                    'value' => $value->get_value(),
                ],
            ],
        ]);

        return $bindings;
    }

}