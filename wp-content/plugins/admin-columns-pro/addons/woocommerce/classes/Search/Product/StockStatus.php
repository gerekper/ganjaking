<?php

namespace ACA\WC\Search\Product;

use AC;
use AC\Helper\Select\Options;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class StockStatus extends Comparison
    implements Comparison\Values
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        if ( ! in_array($value->get_value(), ['instock', 'outofstock', 'onbackorder'], true)) {
            return $bindings;
        }

        $alias_pml = $bindings->get_unique_alias('pml_stock');

        return $bindings->join(
            "LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup AS $alias_pml 
                        ON $wpdb->posts.ID = $alias_pml.product_id"
        )->where($wpdb->prepare("$alias_pml.stock_status = %s", $value->get_value()));
    }

    public function get_values(): Options
    {
        return AC\Helper\Select\Options::create_from_array([
            'instock'     => __('In stock', 'codepress-admin-columns'),
            'outofstock'  => __('Out of stock', 'codepress-admin-columns'),
            'onbackorder' => __('On backorder', 'codepress-admin-columns'),
        ]);
    }

}