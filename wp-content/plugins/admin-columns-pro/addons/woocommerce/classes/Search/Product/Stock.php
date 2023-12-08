<?php

declare(strict_types=1);

namespace ACA\WC\Search\Product;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Stock extends Comparison
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::BETWEEN,
                Operators::LTE,
                Operators::GTE,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ]),
            Value::INT,
            new Labels([
                Operators::IS_EMPTY     => __('is out of stock', 'codepress-admin-columns'),
                Operators::NOT_IS_EMPTY => __('is in stock', 'codepress-admin-columns'),
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias_pml = $bindings->get_unique_alias('pml_stock');

        $join = "LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup AS $alias_pml 
                        ON $wpdb->posts.ID = $alias_pml.product_id";

        switch ($operator) {
            case Operators::EQ :
                $stock = (int)$value->get_value();

                if ($stock > 0) {
                    return $bindings->join($join)
                                    ->where($wpdb->prepare("$alias_pml.stock_quantity = %d", $stock));
                }

                return $bindings->join($join)
                                ->where("$alias_pml.stock_status != 'instock'");
            case Operators::BETWEEN :
            case Operators::GTE :
            case Operators::LTE :
                $comparison = ComparisonFactory::create("$alias_pml.stock_quantity", $operator, $value);

                return $bindings->join($join)
                                ->where($comparison());
            case Operators::IS_EMPTY :
                return $bindings->join($join)
                                ->where("$alias_pml.stock_status != 'instock'");
            case Operators::NOT_IS_EMPTY :
                return $bindings->join($join)
                                ->where("$alias_pml.stock_status = 'instock'");
            default :
                return $bindings;
        }
    }

}