<?php
/**
 * WooCommerce Address Validation
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Address Validation Handler class
 *
 * Handles address validation / postcode lookup
 *
 * @since 1.0
 */
class WC_Address_Validation_Handler {


	/** @var array loaded provider class names*/
	public $provider_class_names;

	/** @var null|\WC_Address_Validation_Provider active provider instance */
	private $active_provider;

	/** @var \WC_Address_Validation_Provider[] loaded provider instances */
	private $providers;


	/**
	 * Load providers and setup hooks
	 *
	 * @since 1.0
	 */
	public function __construct() {

		/**
		 * Filters the active providers list.
		 *
		 * Allows third parties to add more providers.
		 *
		 * @since 1.0
		 *
		 * @param string[] $provider_class_names array of class names
		 */
		$this->provider_class_names = (array) apply_filters( 'wc_address_validation_providers', array(
			'WC_Address_Validation_Provider_Addressy',
			'WC_Address_Validation_Provider_SmartyStreets',
			'WC_Address_Validation_Provider_Crafty_Clicks',
			'WC_Address_Validation_Provider_Postcode_Anywhere',
			'WC_Address_Validation_Provider_Postcode_Software_Dot_Net',
			'WC_Address_Validation_Provider_Postcode_Dot_Nl'
		) );

		$this->maybe_set_default_provider();

		// load active ('wp' is the earliest hook with access to page conditionals)
		add_action( 'wp', array( $this, 'load_validation' ) );

		// load validation javascript
		add_action( 'wp_enqueue_scripts', array( $this, 'load_validation_js' ) );

		// save latitude/longitude/address classification to order
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_address_meta' ) );
	}


	/**
	 * Add hooks to show postcode lookup / address validation forms (only on checkout page)
	 *
	 * @since 1.0
	 */
	public function load_validation() {

		// only load on checkout or edit-address pages and if an active provider is set and configured
		if ( ! $this->is_validation_required() ) {
			return;
		}

		// add postcode lookup if supported
		if ( is_object( $this->get_active_provider() ) && $this->get_active_provider()->supports( 'postcode_lookup' ) && $this->get_active_provider()->is_configured() ) {

			add_action( 'wp_footer', array( $this, 'show_billing_postcode_lookup' ), 0 );

			if ( WC()->cart->needs_shipping_address() || is_account_page() ) {
				add_action( 'wp_footer', array( $this, 'show_shipping_postcode_lookup' ), 0 );
			}
		}

		/* address validation loaded with JS */
	}


	/**
	 * Load javascript for postcode/address validation on checkout page
	 *
	 * @since 1.0
	 */
	public function load_validation_js() {

		$active_provider = $this->get_active_provider();

		// only load on checkout or edit-address pages and if an active provider is set and configured
		if ( null === $active_provider || ! $this->is_validation_required() || ! $active_provider->is_configured() ) {
			return;
		}

		$params = [
			'nonce'                 => wp_create_nonce( 'wc_address_validation' ),
			'locale'                => get_locale(),
			'is_checkout'           => is_checkout(),
			'debug_mode'            => wc_address_validation()->is_debug_mode_enabled(),
			'force_postcode_lookup' => 'yes' === get_option( 'wc_address_validation_force_postcode_lookup' ),
			'ajax_url'              => admin_url( 'admin-ajax.php', 'relative' ),
			'countries'             => $active_provider->get_supported_countries(),
			'billing_postcode'      => is_user_logged_in() ? get_user_meta( get_current_user_id(), 'billing_postcode', true ) : '',
			'shipping_postcode'     => is_user_logged_in() ? get_user_meta( get_current_user_id(), 'shipping_postcode', true ) : '',
			'ajax_loader_url'       => wc_address_validation()->get_framework_assets_url() . '/images/ajax-loader.gif',
		];

		// load postcode lookup JS
		if ( $this->get_active_provider()->supports( 'postcode_lookup' ) ) {

			wp_enqueue_script( 'wc_address_validation_postcode_lookup', wc_address_validation()->get_plugin_url() . '/assets/js/frontend/wc-address-validation-postcode-lookup.min.js', array( 'jquery', 'woocommerce' ), \WC_Address_Validation::VERSION, true );

			wp_localize_script( 'wc_address_validation_postcode_lookup', 'wc_address_validation_postcode_lookup', $params );

			// Add a bit of CSS to ensure themes are not hiding the postcode lookup results
			echo '<style type="text/css">.wc-address-validation-results.form-row { overflow: visible !important; }</style>';
		}

		// load address validation JS
		if ( $this->get_active_provider()->supports( 'address_validation' ) ) {

			if ( 'addressy' === $this->get_active_provider()->id ) {

				wp_enqueue_script( 'wc_addressy_sdk_scripts', '//api.addressy.com/js/address-3.50.min.js', null, '3.50', true );
				wp_enqueue_style( 'wc_addressy_sdk_styles', '//api.addressy.com/css/address-3.50.min.css', null, '3.50' );

				/**
				 * Filters the array of addresses to validate with Addressy
				 *
				 * Expects an associative array in the form of 'address_id' => mapped fields
				 *
				 * @since 2.0.0
				 * @param array
				 */
				$addresses = apply_filters( 'wc_address_validation_addressy_addresses', array(
					'billing' => array(
						array( 'element' => 'billing_company',   'field' => 'Company',      'mode' => 'DEFAULT|PRESERVE' ),
						array( 'element' => 'billing_address_1', 'field' => 'Line1' ),
						array( 'element' => 'billing_address_2', 'field' => 'Line2',        'mode' => 'POPULATE' ),
						array( 'element' => 'billing_city',      'field' => 'City',         'mode' => 'POPULATE' ),
						array( 'element' => 'billing_state',     'field' => 'ProvinceCode', 'mode' => 'POPULATE' ),
						array( 'element' => 'billing_postcode',  'field' => 'PostalCode',   'mode' => 'POPULATE' ),
					),
					'shipping' => array(
						array( 'element' => 'shipping_company',   'field' => 'Company',      'mode' => 'DEFAULT|PRESERVE' ),
						array( 'element' => 'shipping_address_1', 'field' => 'Line1' ),
						array( 'element' => 'shipping_address_2', 'field' => 'Line2',        'mode' => 'POPULATE' ),
						array( 'element' => 'shipping_city',      'field' => 'City',         'mode' => 'POPULATE' ),
						array( 'element' => 'shipping_state',     'field' => 'ProvinceCode', 'mode' => 'POPULATE' ),
						array( 'element' => 'shipping_postcode',  'field' => 'PostalCode',   'mode' => 'POPULATE' ),
						array( 'element' => 'shipping_country',   'field' => 'CountryIso2',  'mode' => 'COUNTRY' ),
					),
				) );

				$params = array_merge( $params, array(
					'addressy_service_key'             => $this->get_active_provider()->service_key,
					'validate_international_addresses' => 'yes' === $this->get_active_provider()->validate_international_addresses,
					'addresses'                        => $addresses,
				) );

				// load the addressy address validation script
				wp_enqueue_script( 'wc_address_validation_addressy', wc_address_validation()->get_plugin_url() . '/assets/js/frontend/wc-address-validation-addressy.min.js', null, \WC_Address_Validation::VERSION, true );
				wp_localize_script( 'wc_address_validation_addressy', 'wc_address_validation', $params );

			} elseif ( 'smartystreets' === $this->get_active_provider()->id ) {

				// load SmartyStreets LiveAddress jQuery plugin
				wp_enqueue_script( 'wc_smartystreets_liveaddress', wc_address_validation()->get_plugin_url() . '/assets/js/vendor/jquery.liveaddress.js', array( 'jquery' ), '5.1.9', true );

				/**
				 * Filters the array of addresses to validate with SmartyStreets.
				 *
				 * Expects an associative array in the form of 'address_id' => mapped fields
				 *
				 * @since 1.9.3
				 * @param array
				 */
				$addresses = apply_filters( 'wc_address_validation_smarty_streets_addresses', array(
					'billing'  => array(
						'address1'            => '#billing_address_1',
						'address2'            => '#billing_address_2',
						'locality'            => '#billing_city',
						'administrative_area' => '#billing_state',
						'country'             => '#billing_country',
						'postal_code'         => '#billing_postcode',
					),
					'shipping' => array(
						'address1'            => '#shipping_address_1',
						'address2'            => '#shipping_address_2',
						'locality'            => '#shipping_city',
						'administrative_area' => '#shipping_state',
						'country'             => '#shipping_country',
						'postal_code'         => '#shipping_postcode',
					),
				) );

				$params = array_merge( $params, array(
					'smarty_streets_key' => $this->get_active_provider()->html_key,
					'plus_four_code'     => $this->get_active_provider()->plus_four_code,
					'addresses'          => $addresses,
				) );

				// load the smartystreets address validation script
				wp_enqueue_script( 'wc_address_validation_smartystreets', wc_address_validation()->get_plugin_url() . '/assets/js/frontend/wc-address-validation-smartystreets.min.js', array( 'wc_smartystreets_liveaddress' ), \WC_Address_Validation::VERSION, true );
				wp_localize_script( 'wc_address_validation_smartystreets', 'wc_address_validation', $params );
			}

		}

		/**
		 * Allow other providers to load JS for postcode/address validation on checkout page
		 *
		 * @since 1.0
		 * @param object $active_provider An instance of the active provider's class
		 * @param WC_Address_Validation_Handler $wc_address_validation_handler
		 */
		do_action( 'wc_address_validation_load_js', $this->get_active_provider(), $this );

	}


	/**
	 * Helper function to show billing postcode lookup form
	 *
	 * @since 1.0
	 */
	public function show_billing_postcode_lookup() {

		// Get postcode lookup template
		wc_get_template( 'checkout/form-postcode-lookup.php', array( 'address_type' => 'billing', 'requires_house_number' => ( 'postcode_dot_nl' == $this->get_active_provider()->id ) ), '', wc_address_validation()->get_plugin_path() . '/templates/' );
	}


	/**
	 * Helper function to show shipping postcode lookup form
	 *
	 * @since 1.0
	 */
	public function show_shipping_postcode_lookup() {

		// Get postcode lookup template
		wc_get_template( 'checkout/form-postcode-lookup.php', array( 'address_type' => 'shipping', 'requires_house_number' => ( 'postcode_dot_nl' == $this->get_active_provider()->id ) ), '', wc_address_validation()->get_plugin_path() . '/templates/' );
	}


	/**
	 * Gets the active provider.
	 *
	 * @since 1.0
	 *
	 * @return null|\WC_Address_Validation_Provider
	 */
	public function get_active_provider() {

		$provider = null;

		if ( is_object( $this->active_provider ) ) {

			$provider = $this->active_provider;

		} else {

			$active_provider = get_option( 'wc_address_validation_active_provider', '' );

			if ( is_string( $active_provider ) && class_exists( $active_provider ) ) {
				$provider = $this->active_provider = new $active_provider;
			}
		}

		/**
		 * Filters the active provider.
		 *
		 * @since 2.3.3
		 *
		 * @param null|\WC_Address_Validation_Provider $provider the active provider or null if not set
		 */
		return apply_filters( 'wc_address_validation_get_active_provider', $provider );
	}


	/**
	 * Gets all loaded providers.
	 *
	 * @since 1.0
	 *
	 * @return array|\WC_Address_Validation_Provider[]
	 */
	public function get_providers() {

		if ( empty( $this->providers ) ) {

			$providers = array();

			foreach ( $this->provider_class_names as $provider_class ) {

				if ( class_exists( $provider_class ) ) {
					$providers[ $provider_class ] = new $provider_class();
				}
			}

			$this->providers = $providers;
		}

		if( self::is_smarty_street_retired() ) {
			unset( $this->providers['WC_Address_Validation_Provider_SmartyStreets'] );
		}

		return $this->providers;
	}


	/**
	 * Saves latitude / longitude / address classification as order meta if enabled & available.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id order ID to save to
	 */
	public function save_address_meta( $order_id ) {

		$order = wc_get_order( $order_id );

		// geocoding
		if ( 'yes' === get_option( 'wc_address_validation_geocode_addresses' ) ) {

			// latitude
			if ( isset( $_POST['wc_address_validation_latitude'] ) && ! empty( $_POST['wc_address_validation_latitude'] ) && is_numeric( $_POST['wc_address_validation_latitude'] ) ) {
				$order->update_meta_data( '_wc_address_validation_latitude', trim( $_POST['wc_address_validation_latitude'] ) );
			}

			// longitude
			if ( isset( $_POST['wc_address_validation_longitude'] ) && ! empty( $_POST['wc_address_validation_longitude'] ) && is_numeric( $_POST['wc_address_validation_latitude'] ) ) {
				$order->update_meta_data( '_wc_address_validation_longitude', trim( $_POST['wc_address_validation_longitude'] ) );
			}
		}

		// address classification
		if ( 'yes' === get_option( 'wc_address_validation_classify_addresses' ) && isset( $_POST['wc_address_validation_classification'] ) && ! empty( $_POST['wc_address_validation_classification'] ) ) {
			$order->update_meta_data( '_wc_address_validation_classification', trim( $_POST['wc_address_validation_classification'] ) );
		}

		$order->save_meta_data();
	}


	/**
	 * Checks if validation should be loaded on the current page
	 *
	 * @since 1.3.2
	 * @return bool true if validation should be loaded
	 */
	public function is_validation_required() {
		global $wp;

		// check if active_provider is set and we are on the checkout page or edit-address page
		$validation_required = ( is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) )
				|| isset( $wp->query_vars['edit-address'] );

		// in some situations `is_checkout()` can be true in the admin so we must ensure we're not in the admin
		$validation_required = ! is_admin() && $validation_required;

		// One Page Checkout
		if ( ! $validation_required && function_exists( 'is_wcopc_checkout' ) ) {
			$validation_required =  is_wcopc_checkout();
		}

		/**
		 * Filter if validation should be loaded on the current page
		 *
		 * @since 1.3.3
		 * @param bool $validation_reqired True if validation should be loaded, false otherwise
		 */
		return apply_filters( 'wc_address_validation_validation_required', $validation_required );
	}

	/**
	 * Is smarty street api retired
	 *
	 * @since 2.9.0
	 * @return boolean
	 */
	public static function is_smarty_street_retired() {

		$current_date = strtotime( wp_date( 'd-m-Y' ) );

		// Make sure date according the WordPress time zone.
		$smarty_retire_date =  strtotime( wp_date( 'd-m-Y', strtotime( '04-10-2022' ) ) );

		return $current_date >= $smarty_retire_date;
	}

	/**
	 * Set the default provider if needed
	 *
	 * @since 2.9.0
	 */
	public function maybe_set_default_provider() {

		$active_provider = get_option( 'wc_address_validation_active_provider', '' );

		if( self::is_smarty_street_retired() && 'WC_Address_Validation_Provider_SmartyStreets' == $active_provider ) {
			update_option( 'wc_address_validation_active_provider', 'WC_Address_Validation_Provider_Addressy' );
			update_option( 'wc_address_validation_smartystreets_retired_message','true' );

		}
	}

}
