<?php

namespace ACA\WC\Search\Order\Address;

use ACA\WC\Search;
use ACA\WC\Type\AddressType;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class FullName extends ACP\Search\Comparison
{

    use Search\Order\OperatorMappingTrait;

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        parent::__construct(
            new Operators([
                Operators::CONTAINS,
            ])
        );

        $this->address_type = $address_type;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                'relation' => 'OR',
                [
                    'field'   => $this->address_type . '_first_name',
                    'value'   => $value->get_value(),
                    'compare' => 'LIKE',
                ],
                [
                    'field'   => $this->address_type . '_last_name',
                    'value'   => $value->get_value(),
                    'compare' => 'LIKE',
                ],
            ],
        ]);

        return $bindings;
    }

}