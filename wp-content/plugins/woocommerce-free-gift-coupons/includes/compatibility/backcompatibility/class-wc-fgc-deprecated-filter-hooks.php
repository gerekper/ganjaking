<?php
/**
 * Deprecated filter hooks
 *
 * @package WooCommerce Free Gift Coupons/Compatibility
 * @since   3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles deprecation notices and triggering of legacy filter hooks
 */
class WC_FGC_Deprecated_Filter_Hooks extends WC_Deprecated_Filter_Hooks {

	/**
	 * Array of deprecated hooks we need to handle.
	 * Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'wc_fgc_apply_coupon_data' => 'woocommerce_free_gift_coupon_apply_coupon_data',
		'wc_fgc_coupon_data'       => 'woocommerce_free_gift_coupon_data',
		'wc_fgc_types'             => 'woocommerce_free_gift_coupon_types',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'woocommerce_free_gift_coupon_apply_coupon_data' => '3.0.0',
		'woocommerce_free_gift_coupon_data'              => '3.0.0',
		'woocommerce_free_gift_coupon_types'             => '3.0.0',
	);

}
