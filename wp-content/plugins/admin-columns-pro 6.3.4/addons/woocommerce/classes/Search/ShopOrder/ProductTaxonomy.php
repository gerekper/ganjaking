<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class ProductTaxonomy extends Comparison
	implements Comparison\SearchableValues {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;

		$operators = new ACP\Search\Operators(
			[
				ACP\Search\Operators::EQ,
			]
		);

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings();

		return $bindings->where( $this->get_where( $value->get_value() ) );
	}

	/**
	 * @param int $product_id
	 *
	 * @return string
	 */
	public function get_where( $product_id ) {
		global $wpdb;
		$orders = $this->get_orders_ids_by_product_cat( $product_id );

		if ( empty( $orders ) ) {
			$orders = [ 0 ];
		}

		return sprintf( "{$wpdb->posts}.ID IN( %s )", implode( ',', $orders ) );
	}

	public function get_values( $s, $paged ) {
		$entities = new ACP\Helper\Select\Entities\Taxonomy( [
			's'        => $s,
			'page'     => $paged,
			'taxonomy' => [ $this->taxonomy ],
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\TermName( $entities )
		);
	}

	/**
	 * Get All orders IDs for a given product ID.
	 *
	 * @param integer $product_id
	 *
	 * @return array
	 */
	protected function get_orders_ids_by_product_cat( $term_id ) {
		global $wpdb;

		$ids = get_posts( [
			'post_type'       => 'product',
			'fields'          => 'ids',
			'posts_per_field' => -1,
			'tax_query'       => [
				[
					'taxonomy' => $this->taxonomy,
					'terms'    => $term_id,
				],
			],
		] );

		$sql = sprintf( "
	        SELECT order_items.order_id
	        FROM {$wpdb->prefix}woocommerce_order_items as order_items
	        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
	        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
	        WHERE posts.post_type = 'shop_order'
	        AND order_items.order_item_type = 'line_item'
	        AND ( order_item_meta.meta_key = '_product_id' OR order_item_meta.meta_key = '_variation_id' )
	        AND order_item_meta.meta_value IN(%s)
        ", implode( ',', $ids ) );

		return $wpdb->get_col( $sql );
	}

}