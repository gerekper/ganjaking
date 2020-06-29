<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly




if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! function_exists( 'yith_initialize_plugin_fw' ) ) {
	/**
	 * Initialize plugin-fw
	 */
	function yith_initialize_plugin_fw( $plugin_dir ) {
		if ( ! function_exists( 'yit_deactive_free_version' ) ) {
			require_once $plugin_dir . 'plugin-fw/yit-deactive-plugin.php';
		}

		if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
			require_once $plugin_dir . 'plugin-fw/yit-plugin-registration-hook.php';
		}

		/* Plugin Framework Version Check */
		if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( $plugin_dir . 'plugin-fw/init.php' ) ) {
			require_once( $plugin_dir . 'plugin-fw/init.php' );
		}
	}
}

if ( ! function_exists( 'yith_ywbc_install_woocommerce_admin_notice' ) ) {

	function yith_ywbc_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH WooCommerce Barcodes is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_ywbc_install' ) ) {
	/**
	 * Install the plugin
	 */
	function yith_ywbc_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywbc_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywbc_init' );
		}
	}
}

if ( ! function_exists( 'yith_ywbc_init' ) ) {
	/**
	 * Start the plugin
	 */
	function yith_ywbc_init() {
		/**
		 * Load text domain
		 */
		load_plugin_textdomain( 'yith-woocommerce-barcodes', false, dirname( YITH_YWBC_BASENAME ) . '/languages/' );

		/** include plugin's files */

		require_once( YITH_YWBC_INCLUDES_DIR . 'class-yith-woocommerce-barcodes.php' );
		require_once( YITH_YWBC_INCLUDES_DIR . 'class-yith-barcode.php' );
		require_once( YITH_YWBC_INCLUDES_DIR . 'class-ywbc-plugin-fw-loader.php' );

		YITH_YWBC();
	}
}

if ( ! function_exists( 'ywbc_main' ) ) {
	/**
	 * Instantiate the plugin main file
	 *
	 * @author      Lorenzo Giuffrida
	 * @since       1.0.0
	 * @deprecated  1.0.9
	 * @return YITH_WooCommerce_Barcodes
	 */
	function ywbc_main() {
		_deprecated_function( 'ywbc_main', '1.0.9', 'YITH_YWBC' );

		return YITH_YWBC();
	}
}

if ( ! function_exists( 'YITH_YWBC' ) ) {
	/**
	 * Instantiate the plugin main file
	 *
	 * @author      Lorenzo Giuffrida
	 * @since       1.0.0
	 * @return YITH_WooCommerce_Barcodes
	 */
	function YITH_YWBC() {
		return YITH_WooCommerce_Barcodes::get_instance();
	}
}

add_action( 'yith_ywbc_init', 'yith_ywbc_init' );


/*
 * Compatibility with Event Tickets
 */
if( ! function_exists( 'yith_wcevti_check_all_order_tickets_in' ) ){
	function yith_wcevti_check_all_order_tickets_in( $order_id ){
		$order = wc_get_order( $order_id );

		if( ! $order ){
			return false;
		}

		$items = $order->get_items();

		if( empty( $items ) ){
			return false;
		}

		foreach( $items as $item ){
			$event_id = isset( $item['_event_id'] ) ? $item['_event_id'] : false;

			if( ! $event_id ){
				continue;
			}

			wp_update_post( array( 'ID' => $event_id, 'post_status' => 'yi-checked' ) );
		}

		return true;

	}
}

