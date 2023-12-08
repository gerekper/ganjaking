<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Helper\Select;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Product extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    use Select\ProductAndVariationValuesTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('filter');

        $product_id = (int)$value->get_value();

        $bindings->join(
            "INNER JOIN {$wpdb->prefix}wc_order_product_lookup AS $alias ON {$wpdb->prefix}wc_orders.id = $alias.order_id"
        );
        $bindings->where(
            $wpdb->prepare("($alias.product_id = %d OR $alias.variation_id = %d)", $product_id, $product_id)
        );

        return $bindings;
    }

}