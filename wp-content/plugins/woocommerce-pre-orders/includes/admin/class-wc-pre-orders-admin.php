<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Admin
 * @author    WooThemes
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Pre-Orders Admin class.
 */
class WC_Pre_Orders_Admin {

	/**
	 * Setup admin class.
	 */
	public function __construct() {
		// Maybe register taxonomies and add admin options
        add_action( 'admin_init', array( $this, 'maybe_install' ), 6 );

		// Load necessary admin styles / scripts (after giving woocommerce a chance to register their scripts so we can make use of them).
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ), 15 );

		// Admin classes.
		$this->includes();
	}

	/**
	 * Includes.
	 */
	protected function includes() {
		require_once 'class-wc-pre-orders-admin-pre-orders.php';
		require_once 'class-wc-pre-orders-admin-orders.php';
		require_once 'class-wc-pre-orders-admin-products.php';
		require_once 'class-wc-pre-orders-admin-settings.php';
	}

	/**
	 * Set installed option and default settings / terms.
	 */
	public function maybe_install() {
		global $woocommerce;

		$installed_version = get_option( 'wc_pre_orders_version' );

		// Install.
		if ( ! $installed_version ) {

			if ( defined( 'WC_VERSION' ) && ! version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$this->check_term_properly_set();
			}

			$admin_settings = new WC_Pre_Orders_Admin_Settings();

			// Install default settings.
			foreach ( $admin_settings->get_settings() as $setting ) {

				if ( isset( $setting['default'] ) ) {
					update_option( $setting['id'], $setting['default'] );
				}
			}
		}

		// Upgrade - installed version lower than plugin version?
		if ( -1 === version_compare( $installed_version, WC_PRE_ORDERS_VERSION ) ) {

			$this->upgrade( $installed_version );

			// New version number.
			update_option( 'wc_pre_orders_version', WC_PRE_ORDERS_VERSION );
		}
	}

	/**
	 * Handles upgrades.
	 *
	 * @param string $installed_version
	 */
	private function upgrade( $installed_version ) {
		// Always call the method to check for the term being set on upgrades.
		// Seen enough cases where this wasn't set, so need to enforce it.
		if ( defined( 'WC_VERSION' ) && ! version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$this->check_term_properly_set();
		}
	}

	/**
	 * Check if the pre order shop status term is properly set.
	 * if it doesn't exist, we make it.
	 *
	 * @deprecated since WooCommerce 2.2
	 */
	private function check_term_properly_set() {
		if ( ! get_term_by( 'slug', 'pre-ordered', 'shop_order_status' ) ) {
			wp_insert_term( 'pre-ordered', 'shop_order_status' );
		}
	}

	/**
	 * Add Pre-orders screen to woocommerce_screen_ids.
	 *
	 * @param  array $ids
	 *
	 * @return array
	 */
	public function screen_ids( $ids ) {
		$ids[] = 'woocommerce_page_wc_pre_orders';

		return $ids;
	}

	/**
	 * Load admin styles & scripts only on needed pages.
	 *
	 * @param string $hook_suffix the menu/page identifier
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $woocommerce, $wc_pre_orders, $wp_scripts;

		// Only load on settings / order / product pages.
		if ( 'woocommerce_page_wc_pre_orders' === $hook_suffix || 'edit.php' === $hook_suffix || 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Admin CSS
			wp_enqueue_style( 'wc_pre_orders_admin', $wc_pre_orders->get_plugin_url() . '/assets/css/wc-pre-orders-admin.css', WC_PRE_ORDERS_VERSION );

			// Admin JS
			wp_enqueue_script( 'wc_pre_orders_admin', $wc_pre_orders->get_plugin_url() . '/assets/js/admin/wc-pre-orders-admin' . $suffix . '.js', WC_PRE_ORDERS_VERSION );

			// Load jQuery UI Date/TimePicker on new/edit product page and pre-orders > actions page
			if ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix || 'woocommerce_page_wc_pre_orders' === $hook_suffix ) {

				// Get loaded jQuery version
				$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.8.2';

				// Load jQuery UI CSS while respecting loaded jQuery version
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );

				// Load TimePicker add-on which extends jQuery DatePicker
				wp_enqueue_script( 'jquery_ui_timepicker', $wc_pre_orders->get_plugin_url() . '/assets/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon' . $suffix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.2' );
			}
		}

	}
}

new WC_Pre_Orders_Admin();
