<?php
/**
 * Deprecated action hooks
 *
 * @package WooCommerce Free Gift Coupons/Compatibility
 * @since   3.0.0
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles deprecation notices and triggering of legacy action hooks.
 */
class WC_FGC_Deprecated_Action_Hooks extends WC_Deprecated_Action_Hooks {

	/**
	 * Array of deprecated hooks we need to handle. Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'wc_fgc_applied' => 'woocommerce_free_gift_coupon_applied',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'woocommerce_free_gift_coupon_applied' => '3.0.0',
	);

}
