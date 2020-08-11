<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * ATUM Inventory Management for WooCommerce 
 * https://wordpress.org/plugins/atum-stock-manager-for-woocommerce/
 * 
 * @package Extra Product Options/Compatibility
 * @version 5.0.12.3
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_atum {

	/**
	 * The single instance of the class
	 *
	 * @since 5.0.12.3
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0.12.3
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.3
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_compatibility' ) );
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

		add_filter( 'wc_epo_no_order_get_items', array($this, 'wc_epo_no_order_get_items'), 10, 1 );

	}

	/**
	 * Skip altering order get_items
	 *
	 * @since 5.0.12.3
	 */
	public function wc_epo_no_order_get_items($ret) {
		global $post;

		if ( isset($_POST['action']) && THEMECOMPLETE_EPO_HELPER()->str_startswith( $_POST['action'], 'atum_' ) ){
			$ret = TRUE;
		}

		if (get_post_type($post) === "atum_purchase_order"){
			$ret = TRUE;	
		}

		return $ret;

	}

}
