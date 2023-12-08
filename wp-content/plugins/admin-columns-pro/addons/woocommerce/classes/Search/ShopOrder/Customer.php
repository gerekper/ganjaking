<?php

namespace ACA\WC\Search\ShopOrder;

use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Customer extends Comparison\Meta
    implements Comparison\SearchableValues
{

    use ACP\Search\UserValuesTrait;

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
        ]);

        parent::__construct($operators, '_customer_user');
    }

}