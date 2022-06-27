<?php
/**
 * Deprecated action hooks
 *
 * @package WooCommerce Mix and Match/Compatibility
 * @since   2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles deprecation notices and triggering of legacy action hooks.
 */
class WC_MNM_Deprecated_Action_Hooks extends WC_Deprecated_Action_Hooks {

	/**
	 * Array of deprecated hooks we need to handle. Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
		'wc_mnm_loaded'                        => 'woocommerce_mnm_loaded',
		'wc_mnm_before_mnm_add_to_cart'        => 'woocommerce_mnm_before_mnm_add_to_cart',
		'wc_mnm_after_mnm_add_to_cart'         => 'woocommerce_mnm_after_mnm_add_to_cart',
		'wc_mnm_add_to_cart'                   => 'woocommerce_mnm_add_to_cart',
		'wc_mnm_child_add_to_order'            => 'woocommerce_mnm_child_add_to_order',
		'wc_mnm_item_add_order_item_meta'      => 'woocommerce_mnm_item_add_order_item_meta',
		'wc_mnm_container_add_order_item_meta' => 'woocommerce_mnm_container_add_order_item_meta',
		'wc_mnm_synced'                        => 'woocommerce_mnm_synced',
		'wc_mnm_child_item_details'            => 'woocommerce_mnm_child_item_details',
		'wc_mnm_before_child_items'            => 'woocommerce_before_mnm_items',
		'wc_mnm_after_child_items'             => 'woocommerce_after_mnm_items',
		'wc_mnm_content_loop'                  => 'woocommerce_mnm_content_loop',
		'wc_mnm_add_to_cart_wrap'              => 'woocommerce_mnm_add_to_cart_wrap',
		'wc_mnm_admin_product_options'         => 'woocommerce_mnm_product_options',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
		'woocommerce_mnm_loaded'                        => '2.0.0',
		'woocommerce_mnm_before_mnm_add_to_cart'        => '2.0.0',
		'woocommerce_mnm_after_mnm_add_to_cart'         => '2.0.0',
		'woocommerce_mnm_add_to_cart'                   => '2.0.0',
		'woocommerce_mnm_child_add_to_order'            => '2.0.0',
		'woocommerce_mnm_item_add_order_item_meta'      => '2.0.0',
		'woocommerce_mnm_container_add_order_item_meta' => '2.0.0',
		'woocommerce_mnm_synced'                        => '2.0.0',
		'woocommerce_mnm_child_item_details'            => '2.0.0',
		'woocommerce_before_mnm_items'                  => '2.0.0',
		'woocommerce_after_mnm_items'                   => '2.0.0',
		'woocommerce_mnm_content_loop'                  => '2.0.0',
		'woocommerce_mnm_add_to_cart_wrap'              => '2.0.0',
		'woocommerce_mnm_product_options'               =>  '2.0.0',
	);

}
