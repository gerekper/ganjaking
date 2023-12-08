<?php

namespace ACA\WC\Search\ShopOrder;

use AC\Helper\Select\Options\Paginated;
use ACP\Helper\Select\Taxonomy\LabelFormatter\TermName;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use WP_Term;

class ProductTaxonomy extends Comparison
    implements Comparison\SearchableValues
{

    /**
     * @var string
     */
    private $taxonomy;

    public function __construct($taxonomy)
    {
        $this->taxonomy = $taxonomy;

        $operators = new Operators(
            [
                Operators::EQ,
            ]
        );

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return (new Bindings())->where($this->get_where($value->get_value()));
    }

    /**
     * @param int $product_id
     *
     * @return string
     */
    public function get_where($product_id)
    {
        global $wpdb;
        $orders = $this->get_orders_ids_by_product_cat($product_id);

        if (empty($orders)) {
            $orders = [0];
        }

        return sprintf("{$wpdb->posts}.ID IN( %s )", implode(',', $orders));
    }

    public function format_label($value): string
    {
        $term = get_term($value);

        return $term instanceof WP_Term
            ? (new TermName())->format_label($term)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            'search'   => $search,
            'page'     => $page,
            'taxonomy' => $this->taxonomy,
        ]);
    }

    /**
     * Get All orders IDs for a given product ID.
     *
     * @param integer $product_id
     *
     * @return array
     */
    protected function get_orders_ids_by_product_cat($term_id)
    {
        global $wpdb;

        $ids = get_posts([
            'post_type'       => 'product',
            'fields'          => 'ids',
            'posts_per_field' => -1,
            'tax_query'       => [
                [
                    'taxonomy' => $this->taxonomy,
                    'terms'    => $term_id,
                ],
            ],
        ]);

        $sql = sprintf(
            "
	        SELECT order_items.order_id
	        FROM {$wpdb->prefix}woocommerce_order_items as order_items
	        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
	        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
	        WHERE posts.post_type = 'shop_order'
	        AND order_items.order_item_type = 'line_item'
	        AND ( order_item_meta.meta_key = '_product_id' OR order_item_meta.meta_key = '_variation_id' )
	        AND order_item_meta.meta_value IN(%s)
        ",
            implode(',', $ids)
        );

        return $wpdb->get_col($sql);
    }

}