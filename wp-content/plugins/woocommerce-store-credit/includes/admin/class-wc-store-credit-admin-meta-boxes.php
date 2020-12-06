<?php
/**
 * WooCommerce Store Credit Meta Boxes.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin_Meta_Boxes class.
 */
class WC_Store_Credit_Admin_Meta_Boxes {

	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {
		$this->includes();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 35 );
	}

	/**
	 * Includes any classes we need.
	 *
	 * @since 3.3.0
	 */
	public function includes() {
		include_once 'meta-boxes/class-wc-store-credit-meta-box-coupon-data.php';
		include_once 'meta-boxes/class-wc-store-credit-meta-box-product-data.php';
	}

	/**
	 * Add meta boxes.
	 *
	 * @since 3.3.0
	 *
	 * @global WP_Post $post The current post.
	 */
	public function add_meta_boxes() {
		global $post;

		$screen_id = wc_store_credit_get_current_screen_id();

		if ( 'shop_coupon' !== $screen_id || ! wc_is_store_credit_coupon( $post->ID ) ) {
			return;
		}

		add_meta_box( 'wc-store-credit-coupon-usage', __( 'Store credit usage', 'woocommerce-store-credit' ), 'WC_Store_Credit_Meta_Box_Coupon_Usage::output', 'shop_coupon', 'normal' );
	}
}

return new WC_Store_Credit_Admin_Meta_Boxes();
