<?php

namespace ACA\WC\Search\ShopCoupon;

use AC;
use AC\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Type extends Comparison\Meta
    implements Comparison\Values
{

    /**
     * @var array [ $key => $label ]
     */
    private $types;

    public function __construct(array $types)
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        $this->types = $types;

        parent::__construct($operators, 'discount_type');
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array($this->types);
    }

}