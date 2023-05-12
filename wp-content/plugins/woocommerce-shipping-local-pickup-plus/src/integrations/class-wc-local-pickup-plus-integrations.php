<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * Integrations class.
 *
 * Conditionally loads third party extensions and plugins compatibility code for:
 *
 * - WooCommerce AvaTax
 * - WooCommerce Composite Products
 * - WooCommerce Customer Order CSV Export
 * - WooCommerce Customer Order XML Export Suite
 * - WooCommerce Mix and Match Products
 * - WooCommerce Per Product Shipping
 * - WooCommerce Print Invoices & Packing Lists
 * - WooCommerce Product Bundles
 * - WooCommerce Subscriptions
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Integrations {


	/** @var \SkyVerge\WooCommerce\Local_Pickup_Plus\Integrations\AvaTax WooCommerce AvaTax integration */
	private $avatax;

	/** @var bool whether the WooCommerce AvaTax extension is active */
	private $is_avatax_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Composite_Products WooCommerce Composite Products integration instance  */
	private $composite_products;

	/** @var bool whether the WooCommerce Composite Products extension is active */
	private $is_composite_products_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Customer_Order_CSV_Export WooCommerce Customer Order CSV Export integration instance */
	private $csv_export;

	/** @var bool whether the WooCommerce Customer Order CSV Export extension is active */
	private $is_csv_export_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export WooCommerce Customer Order XML Export integration instance */
	private $xml_export;

	/** @var bool whether the WooCommerce Customer Order XML Export suite is active */
	private $is_xml_export_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Mix_And_Match_Products Mix and Match Products integration instance */
	private $mix_and_match_products;

	/** @var bool whether WooCommerce Mix and Match Products is active */
	private $is_mix_and_match_products_active;

	/** @var \WC_Local_Pickup_Plus_Integration_PIP WooCommerce PIP integration instance */
	private $pip;

	/** @var bool whether WooCommerce PIP is active */
	private $is_pip_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Product_Bundles WooCommerce Product Bundles integration instance */
	private $product_bundles;

	/** @var bool whether WooCommerce Product Bundles is active */
	private $is_product_bundles_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Subscriptions WooCommerce Subscriptions integration instance */
	private $subscriptions;

	/** @var null|bool whether WooCommerce Subscriptions is active */
	private $is_subscriptions_active;


	/**
	 * Loads integrations.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		if ( $this->is_avatax_active() ) {
			$this->avatax = wc_local_pickup_plus()->load_class( '/src/integrations/AvaTax.php', \SkyVerge\WooCommerce\Local_Pickup_Plus\Integrations\AvaTax::class );
		}

		if ( $this->is_composite_products_active() ) {
			$this->composite_products = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-composite-products/class-wc-local-pickup-plus-integration-composite-products.php', 'WC_Local_Pickup_Plus_Integration_Composite_Products' );
		}

		// WooCommerce Customer Order CSV Export
		if ( $this->is_csv_export_active() ) {
			$this->csv_export = wc_local_pickup_plus()->load_class( '/src/integrations/Customer_Order_Coupon_Export.php', \SkyVerge\WooCommerce\Local_Pickup_Plus\Integrations\Customer_Order_Coupon_Export::class );
		}

		// WooCommerce Customer Order XML Export
		if ( $this->is_xml_export_active() ) {
			$this->xml_export = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-customer-order-xml-export-suite/class-wc-local-pickup-plus-integration-customer-order-xml-export.php', 'WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export' );
		}

		if ( $this->is_mix_and_match_products_active() ) {
			$this->mix_and_match_products = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-mix-and-match-products/class-wc-local-pickup-plus-integration-mix-and-match-products.php', 'WC_Local_Pickup_Plus_Integration_Mix_And_Match_Products' );
		}

		// WooCommerce Per Product Shipping
		add_filter( 'woocommerce_per_product_shipping_skip_free_method_local_pickup_plus', '__return_false' );

		// WooCommerce Print Invoices & Packing Lists
		if ( $this->is_pip_active() ) {
			$this->pip = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-pip/class-wc-local-pickup-plus-integration-pip.php', 'WC_Local_Pickup_Plus_Integration_PIP' );
		}

		if ( $this->is_product_bundles_active() ) {
			$this->product_bundles = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-product-bundles/class-wc-local-pickup-plus-integration-product-bundles.php', 'WC_Local_Pickup_Plus_Integration_Product_Bundles' );
		}

		// WooCommerce Subscriptions
		if ( $this->is_subscriptions_active() ) {
			$this->subscriptions = wc_local_pickup_plus()->load_class( '/src/integrations/woocommerce-subscriptions/class-wc-local-pickup-plus-integration-subscriptions.php', 'WC_Local_Pickup_Plus_Integration_Subscriptions' );
		}
	}


	/**
	 * Gets the AvaTax integration instance.
	 *
	 * @since 2.7.5
	 *
	 * @return \SkyVerge\WooCommerce\Local_Pickup_Plus\Integrations\AvaTax
	 */
	public function get_avatax_instance() {

		return $this->avatax;
	}


	/**
	 * Returns the Composite Products integration instance.
	 *
	 * @since 2.2.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Composite_Products
	 */
	public function get_composite_products_instance() {
		return $this->composite_products;
	}


	/**
	 * Returns the CSV Export integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Customer_Order_CSV_Export
	 */
	public function get_csv_export_instance() {
		return $this->csv_export;
	}


	/**
	 * Returns the XML Export integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export
	 */
	public function get_xml_export_instance() {
		return $this->xml_export;
	}


	/**
	 * Returns the Mix and Match Products integration instance.
	 *
	 * @since 2.2.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Mix_And_Match_Products
	 */
	public function get_mix_and_match_products_instance() {
		return $this->mix_and_match_products;
	}



	/**
	 * Returns the PIP integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_PIP
	 */
	public function get_pip_instance() {
		return $this->pip;
	}


	/**
	 * Returns the Product Bundles integration instance.
	 *
	 * @since 2.2.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Product_Bundles
	 */
	public function get_product_bundles_instance() {
		return $this->product_bundles;
	}


	/**
	 * Returns the Subscriptions integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Subscriptions
	 */
	public function get_subscriptions_instance() {
		return $this->subscriptions;
	}


	/**
	 * Checks whether a plugin is installed and active.
	 *
	 * @since 2.2.0
	 *
	 * @param string $plugin plugin shorthand
	 * @param string $main_file the plugin main file to check (without .php extension)
	 * @return bool
	 */
	private function is_plugin_active( $plugin, $main_file ) {

		$main_file   = "{$main_file}.php";
		$active_prop = "is_{$plugin}_active";
		$is_active   = false;

		if ( property_exists( $this, $active_prop ) ) {

			if ( is_bool( $this->$active_prop ) ) {
				$is_active = $this->$active_prop;
			} else {
				$is_active = $this->$active_prop = wc_local_pickup_plus()->is_plugin_active( $main_file );
			}
		}

		return $is_active;
	}


	/**
	 * Determines whether WooCommerce AvaTax is installed and active.
	 *
	 * @since 2.7.5
	 *
	 * @return bool
	 */
	public function is_avatax_active() {

		return $this->is_plugin_active( 'avatax', 'woocommerce-avatax' );
	}


	/**
	 * Checks whether WooCommerce Composite Products is installed and active.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_composite_products_active() {
		return $this->is_plugin_active( 'composite_products', 'woocommerce-composite-products' );
	}


	/**
	 * Checks whether WooCommerce Customer Order CSV Export is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_csv_export_active() {
		return $this->is_plugin_active( 'csv_export', 'woocommerce-customer-order-csv-export' );
	}


	/**
	 * Checks whether WooCommerce Customer Order XML Export Suite is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_xml_export_active() {
		return $this->is_plugin_active( 'xml_export', 'woocommerce-customer-order-xml-export-suite' );
	}


	/**
	 * Checks whether WooCommerce Mix and Match Products is installed and active.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_mix_and_match_products_active() {
		return $this->is_plugin_active( 'mix_and_match_products', 'woocommerce-mix-and-match-products' );
	}


	/**
	 * Checks whether WooCommerce PIP is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_pip_active() {
		return $this->is_plugin_active( 'pip', 'woocommerce-pip' );
	}


	/**
	 * Checks whether WooCommerce Product Bundles is installed and active.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public function is_product_bundles_active() {
		return $this->is_plugin_active( 'product_bundles', 'woocommerce-product-bundles' );
	}


	/**
	 * Checks whether WooCommerce Subscriptions is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_subscriptions_active() {
		return $this->is_plugin_active( 'subscriptions', 'woocommerce-subscriptions' );
	}


}
