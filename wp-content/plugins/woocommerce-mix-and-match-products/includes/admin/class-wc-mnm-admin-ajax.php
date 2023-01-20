<?php
/**
 * WC_MNM_Admin_Ajax class
 *
 * @package  WooCommerce Mix and Match/Admin/Ajax
 * @since    1.7.0
 * @version  2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX meta-box handlers.
 */
class WC_MNM_Admin_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {

		/**
		 * Edit-Order screens.
		 * 
		 * Use admin-ajax.php in the admin so is_admin() is true and we don't lose our minds again.
		 * 
		 * Ajax handler used to fetch form content for editing container order items.
		 * Ajax handler for editing containers in order.
		 */

		// Ajax handler used to fetch form content for populating "Configure/Edit" container order item modals.
		add_action( 'wp_ajax_woocommerce_mnm_get_edit_container_order_item_form', array( 'WC_MNM_Ajax', 'edit_container_order_item_form' ) );

		// Ajax handler for editing containers in manual/editable orders.
		add_action( 'wp_ajax_woocommerce_mnm_update_container_order_item', array( 'WC_MNM_Ajax' , 'update_container_order_item' ) );

	}

	/*
	|--------------------------------------------------------------------------
	| Edit-Order.
	|--------------------------------------------------------------------------
	*/

	/**
	 * True when displaying content in an edit-container order item modal.
	 * 
	 * @deprecated 2.3.0
	 * @return bool
	 */
	public static function is_container_edit_request() {
		wc_deprecated_function( __METHOD__ . '()', '2.3.0', 'Use doing_action( "wp_ajax_woocommerce_mnm_get_edit_container_order_item_form" )' );
		return doing_action( 'wp_ajax_woocommerce_edit_container_in_order' );
	}

	/**
	 * Form content used to populate "Configure/Edit" container order item modals.
	 * 
	 * @deprecated 2.3.0
	 */
	public static function ajax_container_order_item_form() {
		wc_deprecated_function( __METHOD__ . '()', '2.3.0', 'Moved to WC_MNM_Ajax::edit_container_order_item_form' );
		return WC_MNM_Ajax::edit_container_order_item_form();
	}

	/**
	 * Validates edited/configured containers and returns updated order items.
	 * 
	 * @deprecated 2.3.0
	 */
	public static function ajax_edit_container_in_order() {
		wc_deprecated_function( __METHOD__ . '()', '2.3.0', 'Moved to WC_MNM_Ajax::ajax_edit_container_in_order' );
		return WC_MNM_Ajax::ajax_edit_container_in_order();
	}

	/**
	 * Validates user can edit this product.
	 *
	 * @return mixed - If editable will return an array. Otherwise, will return WP_Error.
	 * 
	 * @deprecated 2.3.0
	 */
	protected static function can_edit_container() {
		wc_deprecated_function( __METHOD__ . '()', '2.3.0', 'Moved to WC_MNM_Ajax::can_edit_container' );
		return WC_MNM_Ajax::can_edit_container();
	}

}
WC_MNM_Admin_Ajax::init();