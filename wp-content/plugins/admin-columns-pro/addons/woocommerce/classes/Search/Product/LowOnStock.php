<?php

namespace ACA\WC\Search\Product;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class LowOnStock extends Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
        ], false);

        parent::__construct(
            $operators,
            null,
            new Labels([
                Operators::IS_EMPTY => __('is low on stock'),
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_pml = $bindings->get_unique_alias('lwstck_pml');
        $alias_meta_threshold = $bindings->get_unique_alias('lwstck_thrshld');
        $alias_meta_managed = $bindings->get_unique_alias('lwstck_mngdstck');

        $join_pml = "\nLEFT JOIN {$wpdb->prefix}wc_product_meta_lookup AS $alias_pml 
                        ON $wpdb->posts.ID = $alias_pml.product_id";
        $join_meta_threshold = "\nLEFT JOIN $wpdb->postmeta AS $alias_meta_threshold 
                        ON $wpdb->posts.ID = $alias_meta_threshold.post_id AND $alias_meta_threshold.meta_key = '_low_stock_amount'";
        $join_meta_managed = "\nLEFT JOIN $wpdb->postmeta AS $alias_meta_managed 
                        ON $wpdb->posts.ID = $alias_meta_managed.post_id AND $alias_meta_managed.meta_key = '_manage_stock'";

        $global_threshold = (int)get_option('woocommerce_notify_low_stock_amount', 0);

        $bindings->join($join_pml . $join_meta_threshold . $join_meta_managed);

        $when = "\nWHEN $alias_meta_managed.meta_value = 'yes' AND $alias_meta_threshold.meta_value > 0 THEN $alias_pml.stock_quantity <= $alias_meta_threshold.meta_value";

        if ($global_threshold > 0) {
            $when .= "\nWHEN $alias_meta_managed.meta_value = 'yes' THEN $alias_pml.stock_quantity <= $global_threshold";
        }

        $bindings->where("CASE $when ELSE 0 END");

        return $bindings;
    }

}