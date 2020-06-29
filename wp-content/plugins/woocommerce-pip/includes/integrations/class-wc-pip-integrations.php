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
 * Integrations class
 * for third party extensions and plugins compatibility
 *
 * @since 3.0.0
 */
class WC_PIP_Integrations {


	/** @var null|\WC_PIP_Integration_Subscriptions instance */
	private $subscriptions;

	/** @var null|\SkyVerge\WooCommerce\PIP\Integration\Product_Add_Ons instance */
	private $add_ons;

	/** @var null|\WC_PIP_Integration_VAT_Number instance */
	private $vat_numbers;


	/**
	 * Load integrations
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// Subscriptions
		if ( wc_pip()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {
			$this->subscriptions = wc_pip()->load_class( '/includes/integrations/woocommerce-subscriptions/class-wc-pip-integration-subscriptions.php', 'WC_PIP_Integration_Subscriptions' );
		}

		if ( wc_pip()->is_plugin_active( 'woocommerce-product-addons.php' ) ) {

			require_once( wc_pip()->get_plugin_path() . '/includes/integrations/woocommerce-product-addons/class-wc-pip-integration-product-add-ons.php' );

			$this->add_ons = new \SkyVerge\WooCommerce\PIP\Integration\Product_Add_Ons();
		}

		// VAT Number Plugins
		$this->vat_numbers = wc_pip()->load_class( '/includes/integrations/vat-number/class-wc-pip-integration-vat-number.php', 'WC_PIP_Integration_VAT_Number' );
	}


	/**
	 * Returns the Subscriptions integration handler instance.
	 *
	 * @since 3.6.1
	 *
	 * @return null|\WC_PIP_Integration_Subscriptions
	 */
	public function get_subscriptions_instance() {

		return $this->subscriptions;
	}


	/**
	 * Returns the Product Add Ons integration handler instance.
	 *
	 * @since 3.6.2
	 *
	 * @return null|\SkyVerge\WooCommerce\PIP\Integration\Product_Add_Ons
	 */
	public function get_product_add_ons_instance() {

		return $this->add_ons;
	}


	/**
	 * Returns the VAT Numbers integration handler instance.
	 *
	 * @since 3.6.1
	 *
	 * @return null|\WC_PIP_Integration_VAT_Number
	 */
	public function get_vat_numbers_instance() {

		return $this->vat_numbers;
	}


}
