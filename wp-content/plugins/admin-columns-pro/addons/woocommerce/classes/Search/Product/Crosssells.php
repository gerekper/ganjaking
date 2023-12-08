<?php

namespace ACA\WC\Search\Product;

use ACA\WC\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Helper\MetaQuery\SerializedComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class Crosssells extends Comparison\Meta
    implements Comparison\SearchableValues
{

    use Select\ProductValuesTrait;

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, '_crosssell_ids');
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $comparison = SerializedComparisonFactory::create(
            $this->meta_key,
            $operator,
            new Value((int)$value->get_value(), $value->get_type())
        );

        return $comparison();
    }

}