<?php

namespace ACA\WC\Search\User\ShopOrder;

use ACA\WC;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Products extends Comparison
    implements Comparison\SearchableValues
{

    use WC\Helper\Select\ProductValuesTrait;

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    /**
     * @inheritDoc
     */
    protected function create_query_bindings($operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $user_ids = $this->get_user_ids_by_product($value->get_value());

        // Force no results
        if ( ! $user_ids) {
            $user_ids = [0];
        }

        return $bindings->where($wpdb->users . '.ID IN( ' . implode(',', $user_ids) . ')');
    }

    /**
     * @param integer $product_id
     *
     * @return array
     */
    protected function get_user_ids_by_product($product_id)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
	        SELECT pm.meta_value
	        FROM {$wpdb->prefix}woocommerce_order_items AS oi
	        JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim ON oi.order_item_id = oim.order_item_id
	        AND oi.order_item_type = 'line_item'
	        AND ( oim.meta_key = '_product_id' OR oim.meta_key = '_variation_id' )
	        AND oim.meta_value = %s
	        JOIN {$wpdb->postmeta} AS pm ON pm.post_id = oi.order_id AND pm.meta_key = '_customer_user'
        ",
            $product_id
        );

        return $wpdb->get_col($sql);
    }

}