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
 * Integration class for VAT Number plugins
 *
 * @since 3.1.5
 */
class WC_PIP_Integration_VAT_Number {


	/**
	 * Add actions and filters.
	 *
	 * @since 3.1.5
	 */
	public function __construct() {

		if ( $this->is_vat_number_plugin_active() ) {

			// filter the settings
			add_filter( 'wc_pip_invoice_settings',                     array( $this, 'add_vat_number_setting' ) );

			// add VAT number to customer details if customer details are visible
			add_filter( 'wc_pip_document_customer_details',            array( $this, 'add_vat_number_to_customer_details' ), 10, 4 );

			// add VAT number after customer details if customer details are not visible
			add_action( 'wc_pip_order_details_after_customer_details', array( $this, 'add_vat_number_after_customer_details' ), 10, 4 );
		}
	}


	/**
	 * Add setting to enable display of the VAT Number on invoices.
	 *
	 * @since 3.1.5
	 * @param array $invoice_settings The invoice settings array
	 * @return array The filtered settings array
	 */
	public function add_vat_number_setting( $invoice_settings ) {

		$new_invoice_settings = array();

		foreach ( $invoice_settings as $setting ) {

			$new_invoice_settings[] = $setting;

			// insert vat number setting after the show coupon setting
			if ( isset( $setting['id'] ) && 'wc_pip_invoice_show_coupons' === $setting['id'] ) {

				$new_invoice_settings[] = array(
					'id'      => 'wc_pip_invoice_show_vat_number',
					'name'    => __( 'Show customer VAT Number', 'woocommerce-pip' ),
					'desc'    => __( 'Enable if you want to display the customer VAT Number.', 'woocommerce-pip' ),
					'default' => 'yes',
					'type'    => 'checkbox',
				);
			}
		}

		return $new_invoice_settings;
	}


	/**
	 * Add the VAT Number to the customer details on invoices if visible.
	 *
	 * @since 3.1.5
	 * @param array $customer_details An associative array of customer details
	 * @param int $order_id The order ID
	 * @param string $type The document type
	 * @param \WC_PIP_Document $document The document instance
	 * @return array The customer details
	 */
	public function add_vat_number_to_customer_details( $customer_details, $order_id, $type, $document ) {

		// bail if customer details are not visible
		if ( ! $document->show_customer_details() ) {
			return $customer_details;
		}

		// bail if document is not an invoice or if the setting is not enabled
		if ( 'invoice' !== $type || 'yes' !== get_option( 'wc_pip_invoice_show_vat_number', 'yes' ) ) {
			return $customer_details;
		}

		if ( $vat_number = $this->get_vat_number( $order_id ) ) {

			$customer_details['vat-number'] = array(
				'label' => __( 'VAT Number:', 'woocommerce-pip' ),
				'value' => $vat_number,
			);
		}

		return $customer_details;
	}


	/**
	 * Display the VAT Number below the customer details on invoices if not visible.
	 *
	 * @since 3.1.5
	 * @param string $type The document type
	 * @param string $action The current action running on the document
	 * @param \WC_PIP_Document $document The document object
	 * @param \WC_Order $order The order object
	 */
	public function add_vat_number_after_customer_details( $type, $action, $document, $order ) {

		// bail if customer details are visible
		if ( $document->show_customer_details() ) {
			return;
		}

		// bail if document is not an invoice or if the setting is not enabled
		if ( 'invoice' !== $type || 'yes' !== get_option( 'wc_pip_invoice_show_vat_number', 'yes' ) ) {
			return;
		}

		if ( $vat_number = $this->get_vat_number( $order ) ) {
			/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - VAT number */
			printf( __( '%1$sVAT Number:%2$s %3$s', 'woocommerce-pip' ), '<strong>', '</strong>', $vat_number );
		}
	}


	/**
	 * Gets the VAT Number set on the order provided by checking if any known meta key exists.
	 *
	 * @since 3.1.5
	 *
	 * @param int|\WC_Order $order_id the order ID or order object
	 * @return string $vat_number the VAT number
	 */
	private function get_vat_number( $order_id ) {

		$vat_number = '';
		$order      = is_numeric( $order_id ) ? wc_get_order( $order_id ) : $order_id;

		if ( $order instanceof \WC_Order ) {

			/**
			 * Filters the array of supported VAT Number post meta.
			 *
			 * @since 3.1.5
			 *
			 * @param array $vat_number_meta_keys An array of supported VAT Number post meta.
			 */
			$vat_number_meta_keys = (array) apply_filters( 'wc_pip_vat_number_meta_keys', [
				'_vat_number',               // EU VAT number
				'VAT Number',                // EU VAT Compliance / Legacy EU VAT number
				'vat_number',                // Taxamo / EU VAT Assistant
				'_billing_wc_avatax_vat_id', // AvaTax
			] );

			foreach ( $vat_number_meta_keys as $meta_key ) {

				$vat_number = $order->get_meta( $meta_key );

				if ( ! empty( $vat_number ) ) {
					break;
				}
			}
		}

		return is_string( $vat_number ) ? $vat_number : '';
	}


	/**
	 * Check if a plugin that adds VAT numbers to orders is active.
	 *
	 * @since 3.1.5
	 *
	 * @return bool True if a VAT number plugin is active
	 */
	private function is_vat_number_plugin_active() {

		$is_plugin_active = false;

		/**
		 * Filters the array of supported VAT Number plugins.
		 *
		 * @since 3.1.5
		 *
		 * @param string[] $plugins an array of supported plugin filenames
		 */
		$vat_plugins = (array) apply_filters( 'wc_pip_vat_number_plugins', array(
			'woocommerce-avatax.php',           // WooCommerce AvaTax
			'woocommerce-taxamo.php',           // WooCommerce Taxamo
			'woocommerce-eu-vat-number.php',    // WooCommerce EU VAT Number (newer versions)
			'eu-vat-number.php',                // WooCommerce EU VAT Number (older versions)
			'woocommerce-eu-vat-assistant.php', // WooCommerce EU VAT Assistant
			'eu-vat-compliance.php',            // WooCommerce EU VAT Compliance
		) );

		foreach ( $vat_plugins as $plugin ) {

			if ( wc_pip()->is_plugin_active( $plugin ) ) {

				$is_plugin_active = true;
				break;
			}
		}

		return $is_plugin_active;
	}


}
