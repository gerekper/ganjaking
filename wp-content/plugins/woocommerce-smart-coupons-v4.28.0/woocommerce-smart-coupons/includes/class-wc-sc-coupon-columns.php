<?php
/**
 * Handle coupon columns
 *
 * @author      StoreApps
 * @since       4.5.2
 * @version     1.0.1
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Columns' ) ) {

	/**
	 * Class for handling coupon columns
	 */
	class WC_SC_Coupon_Columns {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Columns
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Post type.
		 *
		 * @var string
		 */
		protected $list_table_type = 'shop_coupon';

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
		 * Get single instance of WC_SC_Coupon_Columns
		 *
		 * @return WC_SC_Coupon_Columns Singleton object of WC_SC_Coupon_Columns
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
		 * Pre-fetch any data for the row each column has access to it.
		 *
		 * @param int $post_id Post ID being shown.
		 */
		protected function prepare_row_data( $post_id = 0 ) {

			if ( empty( $post_id ) ) {
				return;
			}

			$coupon_id = 0;
			if ( ! empty( $this->object ) ) {
				$coupon = $this->object;
				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}
			}

			if ( empty( $this->object ) || $coupon_id !== $post_id ) {
				$this->object = new WC_Coupon( $post_id );
			}
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 * @return array
		 */
		public function define_columns( $columns = array() ) {

			if ( ! is_array( $columns ) || empty( $columns ) ) {
				$columns = array();
			}

			$columns['wc_sc_view_orders']     = __( 'Used in orders', 'woocommerce-smart-coupons' );
			$columns['wc_sc_coupon_category'] = __( 'Coupon categories', 'woocommerce-smart-coupons' );

			return $columns;
		}

		/**
		 * Render individual columns.
		 *
		 * @param string $column Column ID to render.
		 * @param int    $post_id Post ID being shown.
		 */
		public function render_columns( $column = '', $post_id = 0 ) {

			$this->prepare_row_data( $post_id );

			if ( ! $this->object ) {
				return;
			}

			if ( ! empty( $column ) ) {
				switch ( $column ) {
					case 'wc_sc_view_orders':
						$this->render_view_orders_column();
						break;
					case 'wc_sc_coupon_category':
						WC_SC_Coupon_Categories::get_instance()->render_coupon_category_column( $post_id );
						break;
				}
			}

		}

		/**
		 * Render columm: view_orders.
		 */
		public function render_view_orders_column() {

			$coupon = $this->object;

			if ( $this->is_wc_gte_30() ) {
				$usage_count = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_usage_count' ) ) ) ? $coupon->get_usage_count() : 0;
			} else {
				$usage_count = ( ! empty( $coupon->usage_count ) ) ? $coupon->usage_count : 0;
			}

			$column_content = '';
			if ( ! empty( $usage_count ) ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_code = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
				} else {
					$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}
				$coupon_usage_url = esc_url( admin_url( sprintf( 'edit.php?s=%s&post_status=all&post_type=shop_order&trigger=woocommerce-smart-coupons', esc_html( $coupon_code ) ) ) );
				$column_content   = sprintf( '<a href="%s" target="_blank"><span class="dashicons dashicons-external"></span></a>', $coupon_usage_url );
			} else {
				$column_content = '&ndash;';
			}

			$column_content = apply_filters( 'wc_sc_view_orders_column_content', $column_content, array( 'coupon' => $coupon ) );
			echo $column_content; // phpcs:ignore

		}

	}

}

WC_SC_Coupon_Columns::get_instance();
