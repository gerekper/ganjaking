<?php
/**
 * WooCommerce Order Status Control
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Accept Hosted Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Accept Hosted Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-authorize-net-sim/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Order Status Control main plugin class.
 *
 * @since 1.2.0
 */
class WC_Order_Status_Control extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.12.3';

	/** @var WC_Order_Status_Control single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'order_status_control';


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-order-status-control',
			)
		);

		// Hook for order status when payment is complete
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'handle_payment_complete_order_status' ), -1, 2 );

		// admin
		if ( is_admin() && ! is_ajax() ) {

			// add general settings
			add_filter( 'woocommerce_general_settings', array( $this, 'add_global_settings' ) );
		}
	}


	/**
	 * Handles completing orders when payment is completed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $order_status the default order status to change the order to
	 * @param int $order_id the ID of the order
	 * @return string the (maybe) modified order status to change to
	 */
	public function handle_payment_complete_order_status( $order_status, $order_id ) {

		switch ( get_option( 'wc_order_status_control_auto_complete_orders', '' ) ) {

			case 'none':
				$order_status = 'processing';
			break;

			case 'all':
				$order_status = 'completed';
			break;

			case 'virtual':

				$order = wc_get_order( $order_id );

				// only modify orders that are being changed to 'processing', which indicates they are not a downloadable-virtual order
				if ( $order && 'processing' === $order_status && in_array( $order->get_status(), [ 'on-hold', 'pending', 'failed' ], true ) ) {

					$virtual_order = false;

					$order_items = $order->get_items();

					if ( count( $order_items ) > 0 ) {

						/** @type \WC_Order_Item_Product[] $order_items */
						foreach ( $order_items as $item ) {

							if ( is_callable( [ $item, 'get_product' ] ) ) {
								$product = $item->get_product();
							} elseif ( is_callable( [ $order, 'get_product_from_item' ] ) ) {
								$product = $order->get_product_from_item( $item );
							} else {
								$product = null;
							}

							// this means a product was deleted and it doesn't exist; break to ensure the admin has to review this order
							if ( ! $product || ! is_callable( [ $product, 'is_virtual' ] ) ) {

								$order->add_order_note( __( 'Order auto-completion skipped: deleted or non-existent product found.', 'woocommerce-order-status-control' ) );

								$virtual_order = false;
								break;
							}

							// once we've found one non-virtual product we know we're done, break out of the loop
							if ( ! $product->is_virtual() ) {

								$virtual_order = false;
								break;
							}

							$virtual_order = true;
						}
					}

					// virtual order, mark as completed
					if ( $virtual_order ) {
						$order_status = 'completed';
					}
				}

			break;

			case 'virtual_downloadable':

				// this option should retain WC core functionality of completing
				// orders that contain only products that are both virtual and
				// downloadable.
				// since our filter should run first, assume if the order status
				// is already completed it should remain that way and set the
				// status to 'processing' otherwise. This saves us from looping
				// through all products again.
				$order_status = 'completed' === $order_status ? 'completed' : 'processing';

			break;
		}

		return $order_status;
	}


	/** Admin methods ******************************************************/


	/**
	 * Inject global settings into the Settings > General page, immediately after the 'Store Notice' setting.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings associative array of WooCommerce settings
	 * @return array associative array of WooCommerce settings
	 */
	public function add_global_settings( $settings ) {

		$updated_settings = array();

		for( $i = 0; $i < sizeof( $settings ); $i++ ) {

			$updated_settings[] = $settings[$i];
			$next_setting = isset( $settings[ $i + 1 ] ) ? $settings[ $i + 1 ] : array();

			// insert our field just before the general options end marker
			if ( ! empty( $next_setting ) ) {
				if ( isset( $next_setting['id'] ) && 'general_options' === $next_setting['id'] && isset( $next_setting['type'] ) && 'sectionend' === $next_setting['type'] ) {
					$updated_settings = array_merge( $updated_settings, $this->get_global_settings() );
				}
			}
		}

		return $updated_settings;
	}


	/**
	 * Returns the global settings array for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return array the global settings
	 */
	public function get_global_settings() {

		return apply_filters( 'wc_order_status_control_global_settings', array(

			// complete all orders upon payment complete
			array(
				'title'    => __( 'Orders to Auto-Complete', 'woocommerce-order-status-control' ),
				'desc_tip' => __( 'Select which types of orders should be changed to completed when payment is received. Default WooCommerce behavior is "Virtual & Downloadable".', 'woocommerce-order-status-control' ),
				'id'       => 'wc_order_status_control_auto_complete_orders',
				'default'  => 'virtual_downloadable',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'none'                 => __( 'None', 'woocommerce-order-status-control' ),
					'all'                  => __( 'All Orders', 'woocommerce-order-status-control' ),
					'virtual'              => __( 'Virtual Orders', 'woocommerce-order-status-control' ),
					'virtual_downloadable' => __( 'Virtual & Downloadable Orders', 'woocommerce-order-status-control' ),
				),
			),
		) );
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Order Status Control Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.3.0
	 * @see wc_order_status_control()
	 * @return \WC_Order_Status_Control
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.2
	 * @see Framework\SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Order Status Control', 'woocommerce-order-status-control' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.2
	 * @see Framework\SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the URL to the settings page.
	 *
	 * @since 1.2
	 * @see Framework\SV_WC_Plugin::is_plugin_settings()
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings' );
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.4.0
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/woocommerce-order-status-control/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.4.0
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-order-status-control/';
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.10.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Order_Status_Control\Lifecycle( $this );
	}

} // end WC_Order_Status_Control

/**
 * Returns the One True Instance of Order Status Control.
 *
 * @since 1.10.0
 *
 * @return \WC_Order_Status_Control
 */
function wc_order_status_control() {

	return WC_Order_Status_Control::instance();
}
