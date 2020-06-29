<?php
/**
 * WooCommerce Print Invoices/Packing Lists
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Admin class.
 *
 * Handles general admin tasks.
 *
 * @since 3.0.0
 */
class WC_PIP_Admin {


	/**
	 * Adds various admin hooks/filters.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// load necessary admin styles / scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'load_styles_scripts' ] );

		// add settings page
		add_filter( 'woocommerce_get_settings_pages', [ $this, 'add_settings_page' ] );
	}


	/**
	 * Loads admin JS/CSS.
	 *
	 * @since 3.0.0
	 *
	 * @param string $hook_suffix current screen hook
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $typenow;

		$is_settings = $hook_suffix === Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id();
		$is_orders   = in_array( $typenow, [ 'product', 'shop_order' ], true );

		// load admin JS/CSS only on settings / order / product pages
		if ( $is_settings || $is_orders ) {

			$css_dependencies = [ 'woocommerce_admin_styles', 'wp-pointer' ];

			wp_enqueue_style( 'wc-pip-admin-styles',  wc_pip()->get_plugin_url() . '/assets/css/admin/wc-pip-admin.min.css', $css_dependencies, \WC_PIP::VERSION );

			$js_dependencies = [ 'jquery', 'wp-pointer' ];

			if ( $is_orders ) {
				$js_dependencies[] = 'wc-backbone-modal';
			}

			wp_enqueue_script( 'wc-pip-admin-scripts', wc_pip()->get_plugin_url() . '/assets/js/admin/wc-pip-admin.min.js', $js_dependencies, \WC_PIP::VERSION, true );

			wp_localize_script( 'wc-pip-admin-scripts', 'wc_pip_admin', [

				'ajax_url'                   => admin_url( 'admin-ajax.php' ),
				'order_actions'              => array_keys( wc_pip()->get_orders_instance()->get_actions() ),
				'order_bulk_actions'         => array_keys( wc_pip()->get_orders_instance()->get_bulk_actions() ),
				'confirm_order_action_nonce' => wp_create_nonce( 'confirm-order-action' ),
				'process_order_action_nonce' => wp_create_nonce( 'process-order-action' ),

				'i18n' => [
					'plugin_name'           => __( 'Print Invoices/Packing Lists', 'woocommerce-pip' ),
					'ready_to_print'        => __( 'Documents to print will open in a new window.', 'woocommerce-pip' ),
					'reset_counter_warning' => __( 'Are you sure you want to reset the invoice number counter? This action could result in duplicate invoice numbers or other unintended consequences.', 'woocommerce-pip' ),
					'close'                 => __( 'Close', 'woocommerce-pip' ),
				],

			] );

			wp_enqueue_media();
		}
	}


	/**
	 * Add settings page
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings associative array
	 * @return array
	 */
	public function add_settings_page( $settings ) {

		$settings[] = wc_pip()->load_class( '/includes/admin/class-wc-pip-settings.php', 'WC_PIP_Settings' );

		return $settings;
	}


}
