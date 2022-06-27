<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * ATUM Inventory Management for WooCommerce
 * https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Atum {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Atum|null
	 * @since 5.0.12.3
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0.12.3
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.3
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 5.0.12.3
	 */
	public function add_compatibility() {

		if ( ! defined( 'ATUM_VERSION' ) ) {
			return;
		}

		add_filter( 'wc_epo_no_order_get_items', [ $this, 'wc_epo_no_order_get_items' ], 10, 1 );

	}

	/**
	 * Skip altering order get_items
	 *
	 * @param boolean $ret if order get_items should be skipped.
	 * @since 5.0.12.3
	 */
	public function wc_epo_no_order_get_items( $ret ) {
		global $post;

		if ( isset( $_REQUEST['action'] ) && THEMECOMPLETE_EPO_HELPER()->str_startswith( sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ), 'atum_' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$ret = true;
		}

		if ( get_post_type( $post ) === 'atum_purchase_order' ) {
			$ret = true;
		}

		return $ret;

	}

}
