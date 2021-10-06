<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * WooCommerce Cost of Goods main plugin class.
 *
 * @since 1.0
 *
 * @property \WC_COG_REST_API $rest_api_handler
 */
class WC_COG extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '2.11.0';

	/** @var WC_COG single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'cog';

	/** @var \WC_COG_Admin instance plugin admin */
	protected $admin;

	/** @var \WC_COG_Admin_Reports instance, le reports */
	protected $admin_reports;

	/** @var \WC_COG_Import_Export_Handler instance, adds support for import/export functionality */
	protected $import_export_handler;

	/** @var \SkyVerge\WooCommerce\COG\AJAX instance */
	private $ajax;

	/** @var \SkyVerge\WooCommerce\COG\Utilities\Previous_Orders_Handler instance */
	private $previous_orders_handler;

	/** @var \WC_COG_Integrations integrations handler instance */
	protected $integrations;


	/**
	 * Constructs the plugin.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-cost-of-goods',
			)
		);

		// set an item's cost when added to an order (WC 3.0+)
		add_action( 'woocommerce_new_order_item', array( $this, 'add_new_order_item_cost' ), 10, 3 );

		// set the order meta when an order is placed from standard checkout
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'set_order_cost_meta' ), 10, 1 );

		// set the order meta when an order is created from the legacy v1-v3 API
		add_action( 'woocommerce_api_create_order', array( $this, 'set_order_cost_meta' ), 10, 1 );

		// WC REST API v2 support (WC 3.0+)
		add_action( 'woocommerce_rest_insert_shop_order_object', array( $this, 'set_order_cost_meta' ), 10, 1 );

		// WC REST API v1 support (WC 2.6+)
		add_action( 'woocommerce_rest_insert_shop_order', array( $this, 'set_order_cost_meta' ), 10, 1 );

		// add negative cost of good item meta for refunds
		add_action( 'woocommerce_refund_created', array( $this, 'add_refund_order_costs' ) );

		// add support for orders programmatically added by the ebay WP-Lister plugin
		add_action( 'wplister_after_create_order', array( $this, 'set_order_cost_meta' ), 10, 1 );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 2.8.0
	 *
	 * @internal
	 */
	public function init_plugin() {

		$this->includes();
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 2.8.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/src/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\COG\Lifecycle( $this );
	}


	/**
	 * Builds the REST API handler instance.
	 *
	 * @since 2.8.0
	 */
	protected function init_rest_api_handler() {

		require_once( $this->get_plugin_path() . '/src/class-wc-cog-rest-api.php' );

		$this->rest_api_handler = new \WC_COG_REST_API( $this );
	}


	/**
	 * Includes required files.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function includes() {

		// COG product functions
		require_once( $this->get_plugin_path() . '/src/class-wc-cog-product.php' );

		// framework background job handlers
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-async-request.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-background-job-handler.php' );

		// set up the integrations handler
		$this->integrations = $this->load_class( '/src/integrations/class-wc-cog-integrations.php', 'WC_COG_Integrations' );

		// background job handler to apply costs to previous orders
		require_once( $this->get_plugin_path() . '/src/utilities/class-wc-cog-previous-orders-handler.php' );
		$this->previous_orders_handler = new \SkyVerge\WooCommerce\COG\Utilities\Previous_Orders_Handler();

		// import/export handler
		// TODO: potentially move this to the /integrations directory and break up into plugin-specific classes {CW 2020-01-02}
		$this->import_export_handler = $this->load_class( '/src/utilities/class-wc-cog-import-export-handler.php', 'WC_COG_Import_Export_Handler' );

		if ( is_admin() ) {
			$this->admin_includes();
		}

		if ( is_ajax() ) {
			require_once( $this->get_plugin_path() . '/src/class-wc-cog-ajax.php' );
			$this->ajax = new \SkyVerge\WooCommerce\COG\AJAX();
		}
	}


	/**
	 * Includes required admin files.
	 *
	 * @since 1.0
	 */
	private function admin_includes() {

		// admin
		$this->admin = $this->load_class( '/src/admin/class-wc-cog-admin.php', 'WC_COG_Admin' );

		// reports
		$this->admin_reports = $this->load_class( '/src/admin/class-wc-cog-admin-reports.php', 'WC_COG_Admin_Reports' );
	}


	/**
	 * Returns the integrations class instance.
	 *
	 * @since 2.7.0
	 *
	 * @return \WC_COG_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Returns the background job handler to apply costs to previous orders.
	 *
	 * @since 2.8.0
	 *
	 * @return \SkyVerge\WooCommerce\COG\Utilities\Previous_Orders_Handler
	 */
	public function get_previous_orders_handler_instance() {

		return $this->previous_orders_handler;
	}


	/**
	 * Returns the admin class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_COG_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the admin reports class instance.
	 *
	 * @since 2.0.0
	 */
	public function get_admin_reports_instance() {

		return $this->admin_reports;
	}


	/**
	 * Returns the import/export handler class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_COG_Import_Export_Handler
	 */
	public function get_import_export_handler_instance() {

		return $this->import_export_handler;
	}


	/**
	 * Returns the AJAX handler instance.
	 *
	 * @since 2.8.0
	 *
	 * @return \SkyVerge\WooCommerce\COG\AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the REST API class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_COG_REST_API
	 */
	public function get_rest_api_instance() {

		return $this->rest_api_handler;
	}


	/** Checkout processing methods *******************************************/


	/**
	 * Sets an item's cost when added to an order.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param int $item_id item ID
	 * @param \WC_Order_Item $item item object
	 * @param int $order_id order ID
	 */
	public function add_new_order_item_cost( $item_id, $item, $order_id ) {

		if ( $item instanceof \WC_Order_Item_Product && $item->get_product() ) {

			$cost     = (float) \WC_COG_Product::get_cost( $item->get_product() );
			$quantity = $this->get_item_quantity( $item );
			$order    = wc_get_order( $order_id );

			/**
			 * Filters an item's cost before storing.
			 *
			 * @since 1.9.0
			 *
			 * @param float|string $item_cost order item cost to set
			 * @param \WC_Order_Item_Product $item order item object
			 * @param \WC_Order $order order object
			 */
			$cost = (float) apply_filters( 'wc_cost_of_goods_set_order_item_cost_meta_item_cost', $cost, $item, $order );

			$this->set_item_cost_meta( $item_id, $cost, $quantity );
		}
	}


	/**
	 * Sets the cost of goods for a given order.
	 *
	 * In WC 3.0+ this simply sums up all of the line item total costs.
	 *
	 * @since 1.9.0
	 *
	 * @param int|\WP_Post|\WC_Order $order_id the order ID, post object, or order object
	 * @param bool $force when not used in callbacks, whether to force recalculation, potentially overwriting previous data
	 */
	public function set_order_cost_meta( $order_id, $force = false ) {

		// get the order object
		$order = wc_get_order( $order_id );

		$total_cost = 0;

		// loop through the order items and set their cost meta
		foreach ( $order->get_items() as $item_id => $item ) {

			// if cost was already added in WC 3.0+ and we're not forcing an update
			if ( ! $force && $item instanceof \WC_Order_Item && $item->get_meta( '_wc_cog_item_total_cost' ) ) {

				$total_cost += (float) $item->get_meta( '_wc_cog_item_total_cost' );

			// otherwise, set the cost meta (also applies to older WC versions)
			} elseif ( $item_id && ! empty( $item ) ) {

				$product_id = ( ! empty( $item['variation_id'] ) ) ? $item['variation_id'] : $item['product_id'];
				$item_cost  = (float) \WC_COG_Product::get_cost( $product_id );
				$quantity   = (float) $item['qty'];

				/**
				 * Filters an item's cost before storing.
				 *
				 * @since 1.9.0
				 *
				 * @param float|string $item_cost order item cost to set
				 * @param \WC_Order_Item_Product $item order item object
				 * @param \WC_Order $order order object
				 */
				$item_cost = (float) apply_filters( 'wc_cost_of_goods_set_order_item_cost_meta_item_cost', $item_cost, $item, $order );

				$this->set_item_cost_meta( $item_id, $item_cost, $quantity );

				// add to the item cost to the total order cost.
				$total_cost += ( $item_cost * $quantity );
			}
		}

		/**
		 * Filters the Order Total Cost.
		 *
		 * Allow actors to modify the order total cost before the meta is updated.
		 *
		 * @since 1.9.0
		 *
		 * @param float|string $total_cost order total cost to set
		 * @param \WC_Order $order order object
		 */
		$total_cost = apply_filters( 'wc_cost_of_goods_set_order_cost_meta', $total_cost, $order );

		$formatted_total_cost = wc_format_decimal( $total_cost, wc_get_price_decimals() );

		// save the order total cost meta
		$order->update_meta_data( '_wc_cog_order_total_cost', $formatted_total_cost );
		$order->save_meta_data();
	}


	/**
	 * Sets an order item's cost meta.
	 *
	 * @since 1.9.0
	 *
	 * @param int $item_id item ID
	 * @param float|string $item_cost item cost
	 * @param float $quantity item quantity
	 */
	protected function set_item_cost_meta( $item_id, $item_cost, $quantity ) {

		if ( empty( $item_cost ) || ! is_numeric( $item_cost ) ) {
			$item_cost = '0';
		}

		// format the single item cost
		$formatted_cost = wc_format_decimal( $item_cost );

		// format the total item cost
		$formatted_total = wc_format_decimal( $item_cost * $quantity );

		try {
			wc_update_order_item_meta( $item_id, '_wc_cog_item_cost', $formatted_cost );
			wc_update_order_item_meta( $item_id, '_wc_cog_item_total_cost', $formatted_total );
		} catch ( \Exception $e ) {}
	}



	/**
	 * Adds order costs to a refund, which are negative values for:
	 *
	 * + refund total cost
	 * + refund line item cost
	 * + refund line item total cost
	 *
	 * These offset the positive values for the order & order items,
	 * which results in accurate reports when amounts are summed across both orders & refunds.
	 *
	 * This matches the WC core behavior where line totals for refund line items are negative.
	 *
	 * @since 2.8.0
	 *
	 * @param int $refund_id refund ID
	 */
	public function add_refund_order_costs( $refund_id ) {

		$refund = wc_get_order( $refund_id );

		$refund_total_cost = 0;

		foreach ( $refund->get_items() as $refund_line_item_id => $refund_line_item ) {

			// skip line items that aren't actually being refunded or the original refunded item ID isn't available
			if (    ! isset( $refund_line_item['line_total'] )
				 ||   $refund_line_item['line_total'] >= 0
				 || ! isset( $refund_line_item['qty'] )
				 ||   abs( $refund_line_item['qty'] ) === 0
				 ||   empty( $refund_line_item['refunded_item_id'] ) ) {

				continue;
			}

			// get original item cost
			try {
				$item_cost = wc_get_order_item_meta( $refund_line_item['refunded_item_id'], '_wc_cog_item_cost', true );
			} catch ( \Exception $e ) {
				$item_cost = null;
			}

			// Skip if a cost wasn't set for the original item.
			// Note that we're intentionally not calculating a negative cost here,
			// based on the current cost for the product because we assume the admin decided
			// not to run the "apply costs" feature to apply historical costs.
			if ( ! $item_cost ) {
				continue;
			}

			// a refunded item cost & item total cost are negative since they reduce the item total costs when summed (for reports, etc)
			$refunded_item_cost       = $item_cost * -1;
			$refunded_item_total_cost = ( $item_cost * abs( $refund_line_item['qty'] ) ) * -1;

			// add as meta to the refund line item
			try {
				wc_update_order_item_meta( $refund_line_item_id, '_wc_cog_item_cost',       wc_format_decimal( $refunded_item_cost ) );
				wc_update_order_item_meta( $refund_line_item_id, '_wc_cog_item_total_cost', wc_format_decimal( $refunded_item_total_cost ) );
			} catch ( \Exception $e ) {
				continue;
			}

			$refund_total_cost += $refunded_item_total_cost;
		}

		/**
		 * Filters the total cost for refunds.
		 *
		 * Allow actors to change the order total cost before it's set when refunding an order in the admin.
		 *
		 * *IMPORTANT*
		 * You must add negative values to this total if you've added costs via the similar order total cost filters above.
		 * It should match the order total cost, just with a negative value.
		 *
		 * @since 2.0.0
		 *
		 * @param float|string $refund_total_cost order total cost to update
		 * @param \WC_Order_Refund $refund refund order object
		 */
		$refund_total_cost = apply_filters( 'wc_cost_of_goods_update_refund_order_cost_meta', $refund_total_cost, $refund );

		// update the refund total cost
		$refund->update_meta_data( '_wc_cog_order_total_cost', wc_format_decimal( $refund_total_cost, wc_get_price_decimals() ) );
		$refund->save_meta_data();
	}


	/**
	 * Gets the item quantity.
	 *
	 * @since 2.9.9
	 *
	 * @param WC_Order_Item_Product $item item object
	 * @return float item quantity
	 */
	private function get_item_quantity( \WC_Order_Item_Product $item ) {

		/**
		 * Filters the item quantity, allowing integrations to override it if necessary.
		 *
		 * @since 2.9.9
		 *
		 * @param float|string $item_quantity item quantity
		 * @param \WC_Order_Item_Product $item item object
		 * @param \WC_COG $cost_of_goods Cost of Goods main plugin class instance
		 */
		return (float) apply_filters( 'wc_cost_of_goods_get_item_quantity', $item->get_quantity(), $item, $this );
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.3
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Cost of Goods', 'woocommerce-cost-of-goods' );
	}


	/**
	 * Returns the full path and filename of the plugin file.
	 *
	 * @since 1.3
	 *
	 * @return string
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the URL to the settings page.
	 *
	 * @since 1.3
	 *
	 * @param string $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products&section=inventory' );
	}


	/**
	 * Returns the plugin documentation url.
	 *
	 * @since 1.8.0
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/cost-of-goods-sold/';
	}


	/**
	 * Returns true if on the plugin settings page.
	 *
	 * @since 1.3
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'], $_GET['section'] )
			&& 'wc-settings' === $_GET['page']
			&& 'products'    === $_GET['tab']
			&& 'inventory'   === $_GET['section'];
	}


	/**
	 * Returns true if on the reports page.
	 *
	 * @since 2.11.0
	 *
	 * @return bool
	 */
	public function is_reports_page() : bool {

		return 'wc-reports' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-cost-of-goods/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the main instance of Cost of Goods instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_cog()
	 *
	 * @since 1.6.0
	 *
	 * @return WC_COG
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}


/**
 * Returns the One True Instance of Cost of Goods.
 *
 * @since 1.6.0
 *
 * @return \WC_COG
 */
function wc_cog() {

	return \WC_COG::instance();
}
