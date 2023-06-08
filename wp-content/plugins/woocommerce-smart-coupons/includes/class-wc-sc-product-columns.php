<?php
/**
 * Class to show related coupons on a product page.
 *
 * @package     woocommerce-smart-coupons/includes/
 * @author      StoreApps
 * @since       7.12.0
 * @version     1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_SC_Product_Columns' ) ) {

	/**
	 * Class for handling product columns
	 */
	class WC_SC_Product_Columns {

		/**
		 * Variable to hold instance of WC_SC_Product_Columns
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Post type.
		 *
		 * @var string
		 */
		protected $list_table_type = 'product';

		/**
		 * Object being shown on the row.
		 *
		 * @var object|null
		 */
		protected $object = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_filter( 'manage_' . $this->list_table_type . '_posts_columns', array( $this, 'define_columns' ), 11 );
			add_action( 'manage_' . $this->list_table_type . '_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );
		}

		/**
		 * Get single instance of WC_SC_Product_Columns
		 *
		 * @return WC_SC_Product_Columns Singleton object of WC_SC_Product_Columns
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 * @return array Updated columns list.
		 */
		public function define_columns( $columns = array() ) {

			if ( ! is_array( $columns ) || empty( $columns ) ) {
				$columns = array();
			}

			$columns['wc_sc_linked_coupons'] = _x( 'Linked coupons', 'Title for coupon column on the products page', 'woocommerce-smart-coupons' );

			return $columns;
		}

		/**
		 * Pre-fetch any data for the row each column has access to it.
		 *
		 * @param int $post_id Post ID being shown.
		 */
		protected function prepare_row_data( $post_id = 0 ) {

			if ( empty( $post_id ) ) {
				return;
			}

			$product_id = 0;
			if ( ! empty( $this->object ) ) {
				$product = $this->object;
				if ( $this->is_wc_gte_30() ) {
					$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
				} else {
					$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
				}
			}

			if ( empty( $this->object ) || $product_id !== $post_id ) {
				$this->object = wc_get_product( $post_id );
			}

		}

		/**
		 * Render individual columns.
		 *
		 * @param string  $column Column ID to render.
		 * @param integer $post_id Post ID being shown.
		 */
		public function render_columns( $column = '', $post_id = 0 ) {

			if ( empty( $post_id ) ) {
				return;
			}

			if ( empty( $column ) ) {
				return;
			}

			$this->prepare_row_data( $post_id );

			switch ( $column ) {
				case 'wc_sc_linked_coupons':
					$this->render_view_product_coupon_column( $post_id, $this->object );
					break;
			}

		}

		/**
		 * Render linked coupons column on product screen.
		 *
		 * @param integer    $product_id The Product ID.
		 * @param WC_Product $product The Product object.
		 */
		public function render_view_product_coupon_column( $product_id = 0, $product = null ) {

			if ( empty( $product_id ) ) {
				return;
			}

			$max_coupons_limit = apply_filters(
				'wc_sc_maximum_linked_coupons_limit',
				$this->sc_get_option( 'wc_sc_maximum_linked_coupons_limit', 5 ),
				array(
					'source'      => $this,
					'product_id'  => $product_id,
					'product_obj' => $product,
				)
			);

			// Fetch linked coupons from simple & variable products (except variations).
			$linked_coupons = ( $this->is_callable( $product, 'get_meta' ) ) ? $product->get_meta( '_coupon_title' ) : $this->get_post_meta( $product_id, '_coupon_title', true );

			if ( empty( $linked_coupons ) || ! is_array( $linked_coupons ) ) {
				$linked_coupons = array();
			} else {
				$linked_coupons = array_filter( array_unique( $linked_coupons ) );
			}

			$linked_coupons_count = count( $linked_coupons );

			if ( $linked_coupons_count > $max_coupons_limit ) {
				$linked_coupons = array_slice( $linked_coupons, 0, $max_coupons_limit );
			} elseif ( $linked_coupons_count < $max_coupons_limit ) { // Try to find more linked coupons if number of found coupons are not matching $max_coupons_limit.
				// Try to find linked coupons from the variations.
				if ( $product->is_type( 'variable' ) && $product->has_child() ) {
					$children = $product->get_children();
					foreach ( $children as $variation_id ) {
						$variation                = wc_get_product( $variation_id );
						$variation_linked_coupons = ( $this->is_callable( $variation, 'get_meta' ) ) ? $variation->get_meta( '_coupon_title' ) : $this->get_post_meta( $variation_id, '_coupon_title', true );
						if ( empty( $variation_linked_coupons ) || ! is_array( $variation_linked_coupons ) ) {
							continue;
						}
						$linked_coupons       = array_merge( $linked_coupons, $variation_linked_coupons );
						$linked_coupons       = array_filter( array_unique( $linked_coupons ) );
						$linked_coupons_count = count( $linked_coupons );
						if ( $linked_coupons_count === $max_coupons_limit ) {
							break;
						} elseif ( $linked_coupons_count > $max_coupons_limit ) {
							$linked_coupons = array_slice( $linked_coupons, 0, $max_coupons_limit );
							break;
						}
					}
				}
			}

			if ( empty( $linked_coupons ) || ! is_array( $linked_coupons ) ) {
				echo esc_html( '&ndash;' );
				return;
			}

			$linked_coupons = array_values( $linked_coupons );

			$coupon_html = array();
			foreach ( $linked_coupons as $index => $coupon_code ) {
				$coupon_id       = wc_get_coupon_id_by_code( $coupon_code );
				$coupon_edit_url = add_query_arg(
					array(
						'post'   => $coupon_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				);
				$coupon_html[]   = '<a href="' . esc_url( $coupon_edit_url ) . '" target="_blank" title="' . esc_attr__( 'Open in a new tab', 'woocommerce-smart-coupons' ) . '"><code>' . esc_html( $coupon_code ) . '</code></a>';
			}

			$linked_coupons_html = apply_filters(
				'wc_sc_product_column_linked_coupons_html',
				implode( ' , ', $coupon_html ),
				array(
					'source'                       => $this,
					'product_id'                   => $product_id,
					'product_obj'                  => $product,
					'maximum_linked_coupons_limit' => $max_coupons_limit,
					'linked_coupons'               => $linked_coupons,
				)
			);

			echo wp_kses_post( $linked_coupons_html );

		}

	}

}

WC_SC_Product_Columns::get_instance();
