<?php

namespace ACA\WC\Search\Order\Address;

use ACA\WC\Search;
use ACA\WC\Type\AddressType;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class FullAddress extends ACP\Search\Comparison
{

    private $address_type;

    public function __construct(AddressType $address_type)
    {
        parent::__construct(new Operators([Operators::CONTAINS]));

        $this->address_type = $address_type;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings\QueryArguments();

        $bindings->meta_query([
            'key'     => sprintf('_%s_address_index', $this->address_type),
            'value'   => $value->get_value(),
            'compare' => 'LIKE',
        ]);

        return $bindings;
    }

}