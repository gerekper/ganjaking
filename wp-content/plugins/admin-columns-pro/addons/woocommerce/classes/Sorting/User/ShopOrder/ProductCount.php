<?php

namespace ACA\WC\Sorting\User\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\SqlTrait;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class ProductCount implements QueryBindings
{

    use SqlTrait;

    private $status;

    public function __construct(array $status = null)
    {
        if (null === $status) {
            $status = ['wc-completed'];
        }

        $this->status = $status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('count');

        $where = $this->status
            ? sprintf(
                "AND acsort_posts.post_status IN ( %s )",
                $this->esc_sql_array($this->status)
            )
            : '';

        $bindings->join(
            " 
            LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON acsort_postmeta.meta_value = $wpdb->users.ID 
                AND acsort_postmeta.meta_key = '_customer_user'
            LEFT JOIN $wpdb->posts AS acsort_posts ON acsort_postmeta.post_id = acsort_posts.ID
                AND acsort_posts.post_type = 'shop_order'
                $where
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_orderitems ON acsort_orderitems.order_id = acsort_posts.ID 
                AND acsort_orderitems.order_item_type = 'line_item'
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias ON acsort_orderitems.order_item_id = $alias.order_item_id 
                AND $alias.meta_key = '_qty'
		"
        );
        $bindings->group_by("$wpdb->users.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                "$alias.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}