<?php
/**
 * Product Bundle Class
 *
 * @author  Cuong Nguyen
 * @package WBS
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WC_Product_Wbs_Bundle' ) ) {
	/**
	 * Product Bundle Object
	 *
	 */
	class WC_Product_Wbs_Bundle extends WC_Product {

		public $bundle_data;
		private $bundled_items;

		/**
		 * __construct
		 *
		 * @access public
		 *
		 * @param mixed $product
		 */
		public function __construct( $product ) {
			if ( ! $this instanceof WC_Data ) {
				$this->product_type = 'wbs_bundle';
			}

			parent::__construct( $product );

			$this->bundle_data = get_post_meta( $this->get_id(), '_wbs_wcpb_bundle_data', true );

			if ( ! empty( $this->bundle_data ) ) {
				$this->load_items();
			}

		}

		public function get_type() {
			return 'wbs_bundle';
		}

		/**
		 * Load bundled items
		 */
		private function load_items() {
			$virtual = true;
			foreach ( $this->bundle_data as $b_item_id => $b_item_data ) {
				$b_item = new WBS_WC_Bundled_Item( $this, $b_item_id );
				if ( $b_item->exists() ) {
					$this->bundled_items[ $b_item_id ] = $b_item;
					if ( ! $b_item->product->is_virtual() ) {
						$virtual = false;
					}
				}
			}
			$this->virtual = $virtual;
		}

		/**
		 * return bundled items array [or false if it's empty]
		 */
		public function get_bundled_items() {
			return ! empty( $this->bundled_items ) ? $this->bundled_items : array();
		}

		/**
		 * Returns false if the product cannot be bought.
		 */
		public function is_purchasable() {

			$purchasable = true;

			// Products must exist of course
			if ( ! $this->exists() ) {
				$purchasable = false;

				// Other products types need a price to be set
			} elseif ( $this->get_price() === '' ) {
				$purchasable = false;

				// Check the product is published
			} elseif ( ( $this instanceof WC_Data ? $this->get_status() : $this->post->post_status ) !== 'publish' && ! current_user_can( 'edit_post', $this->get_id() ) ) {
				$purchasable = false;
			}

			// Check the bundle items are purchasable

			$bundled_items = $this->get_bundled_items();
			foreach ( $bundled_items as $bundled_item ) {
				if ( ! $bundled_item->get_product()->is_purchasable() ) {
					$purchasable = false;
				}
			}

			return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
		}

		/**
		 * Returns true if all items is in stock
		 */
		public function all_items_in_stock() {
			$response = true;

			$bundled_items = $this->get_bundled_items();
			foreach ( $bundled_items as $bundled_item ) {
				if ( ! $bundled_item->get_product()->is_in_stock() ) {
					$response = false;
				}
			}

			return $response;
		}

		/**
		 * Returns true if one item at least is variable product.
		 */
		public function has_variables() {
			return false;
		}

		/**
		 * Get the add to cart url used in loops.
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_url() {
			$url = $this->is_purchasable() && $this->is_in_stock() && $this->all_items_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() );

			return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
		}

		/**
		 * Get the add to cart button text
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_text() {
			$text = $this->is_purchasable() && $this->is_in_stock() && $this->all_items_in_stock() ? __( 'Add to cart', 'woocommerce-boost-sales' ) : __( 'Read More', 'woocommerce-boost-sales' );

			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

		/**
		 * get the ID of the parent post
		 * added for support to wc 2.7 and wc 2.6
		 *
		 * @param string $context
		 *
		 * @return int
		 */
		public function get_parent_id( $context = 'view' ) {
			return $this instanceof WC_Data ? parent::get_parent_id( $context ) : $this->get_parent();
		}

		/**
		 * Get the title of the post.
		 *
		 * @access public
		 * @return string
		 */
		public function get_title() {

			$title = get_the_title( $this->get_id() );

			if ( $this->get_parent_id() > 0 ) {
				$title = get_the_title( $this->get_parent_id() ) . ' &rarr; ' . $title;
			}

			return apply_filters( 'woocommerce_product_title', $title, $this );
		}

		/**
		 * Sync grouped products with the children lowest price (so they can be sorted by price accurately).
		 *
		 * @access public
		 * @return void
		 */
		public function grouped_product_sync() {
			if ( ! $this->get_parent_id() ) {
				return;
			}

			$children_by_price = get_posts( array(
				'post_parent'    => $this->get_parent_id(),
				'orderby'        => 'meta_value_num',
				'order'          => 'asc',
				'meta_key'       => '_price',
				'posts_per_page' => 1,
				'post_type'      => 'product',
				'fields'         => 'ids'
			) );
			if ( $children_by_price ) {
				foreach ( $children_by_price as $child ) {
					$child_price = get_post_meta( $child, '_price', true );
					update_post_meta( $this->get_parent_id(), '_price', $child_price );
				}
			}

			delete_transient( 'wc_products_onsale' );

			do_action( 'woocommerce_grouped_product_sync', $this->get_id(), $children_by_price );
		}

	}
}
