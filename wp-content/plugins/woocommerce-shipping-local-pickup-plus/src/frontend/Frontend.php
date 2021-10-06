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
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Frontend methods.
 *
 * @since 2.0.0
 */
class Frontend {


	/** @var Cart cart handler instance */
	private $cart;

	/** @var Checkout checkout handler instance */
	private $checkout;


	/**
	 * Frontend constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if ( $local_pickup_plus && $local_pickup_plus->is_available() ) {

			$plugin_path = wc_local_pickup_plus()->get_plugin_path();

			// load data storage objects
			require_once( $plugin_path . '/src/frontend/Data_Store/Pickup_Data.php' );
			require_once( $plugin_path . '/src/frontend/Data_Store/Cart_Item_Pickup_Data.php' );
			require_once( $plugin_path . '/src/frontend/Data_Store/Package_Pickup_Data.php' );

			// load field objects
			require_once( $plugin_path . '/src/frontend/Fields/Field.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Pickup_Location_Field.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Cart_Item_Pickup_Location_Field.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Package_Pickup_Location_Field.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Cart_Item_Handling_Toggle.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Package_Pickup_Appointment_Field.php' );
			require_once( $plugin_path . '/src/frontend/Fields/Package_Pickup_Items_Field.php' );

			// load handlers
			$this->cart     = wc_local_pickup_plus()->load_class( '/src/frontend/Cart.php', 'SkyVerge\WooCommerce\Local_Pickup_Plus\Cart' );
			$this->checkout = wc_local_pickup_plus()->load_class( '/src/frontend/Checkout.php', 'SkyVerge\WooCommerce\Local_Pickup_Plus\Checkout' );

			// add frontend script and styles
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_styles' ] );
		}
	}


	/**
	 * Forces WC to save the session when updating the shipping method.
	 *
	 * This was not actually needed, the problem never was that the session data wasn't saved,
	 * but that it was intentionally reset by WC because the available methods were different.
	 *
	 * @internal
	 *
	 * @since 2.8.3
	 * @deprecated 2.9.7
	 *
	 * TODO remove this method by July 2022 or by version 3.0.0 (whichever comes first) {DM 2021-07-19}
	 *
	 * @param string $template_name template being loaded by WC
	 */
	public function force_save_session_after_updating_shipping_method( $template_name ) {

		wc_deprecated_function( __METHOD__, '2.9.7' );
	}


	/**
	 * Get the cart instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Cart
	 */
	public function get_cart_instance() {
		return $this->cart;
	}


	/**
	 * Get the checkout instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Checkout
	 */
	public function get_checkout_instance() {
		return $this->checkout;
	}


	/**
	 * Load frontend script and styles.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts_styles() {

		if ( is_cart() || is_checkout() ) {

			$this->load_styles();
			$this->load_scripts();
		}
	}

	/**
	 * Load frontend styles.
	 *
	 * @since 2.0.0
	 */
	private function load_styles() {

		$dependencies = [
			'dashicons',
			'select2',
		];

		// by default WooCommerce doesn't load Select2 library in cart page
		if ( ! wp_style_is( 'select2', 'enqueued' ) ) {

			$style_file = 'assets/css/select2.css';
			$style_path = str_replace( [ 'http:', 'https:' ], '', plugins_url($style_file, WC_PLUGIN_FILE ) );

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				wp_register_style( 'select2', $style_path, [], '4.0.3' );
			}

			wp_enqueue_style( 'select2', $style_path, [], '4.0.3' );
		}

		wp_enqueue_style( 'wc-local-pickup-plus-frontend', wc_local_pickup_plus()->get_plugin_url() . '/assets/css/frontend/wc-local-pickup-plus-frontend.min.css', $dependencies, \WC_Local_Pickup_Plus::VERSION );

		/**
		 * Upon enqueueing Local Pickup Plus frontend styles.
		 *
		 * @since 2.0.0
		 *
		 * @param array $styles handlers
		 * @param array $dependencies dependencies handles
		 */
		do_action( 'wc_local_pickup_plus_load_frontend_styles', [ 'wc-local-pickup-plus-frontend' ], $dependencies );
	}


	/**
	 * Load and localize frontend scripts.
	 *
	 * @since 2.0.0
	 */
	private function load_scripts() {

		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', [ 'jquery' ], WC_VERSION, true );

		$dependencies = [
			'jquery',
			'jquery-blockui',
			'jquery-tiptip',
			'jquery-ui-datepicker',
			'select2',
		];

		// WooCommerce may not have loaded Select2 library in cart page
		if ( defined( 'WC_PLUGIN_FILE' ) && ! wp_script_is( 'select2', 'enqueued' ) ) {

			$script_file = 'assets/js/select2/select2.full.js';
			$script_path = str_replace( [ 'http:', 'https:' ], '', plugins_url( $script_file, WC_PLUGIN_FILE ) );

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				wp_register_script( 'select2', $script_path, [ 'jquery' ], '4.0.3', false );
			}

			wp_enqueue_script( 'select2', $script_path, [ 'jquery' ], '4.0.3', false );
		}

		// load scripts
		wp_enqueue_script( 'wc-local-pickup-plus-frontend', wc_local_pickup_plus()->get_plugin_url() . '/assets/js/frontend/wc-local-pickup-plus-frontend.min.js', $dependencies, \WC_Local_Pickup_Plus::VERSION, false );

		$shipping_method = wc_local_pickup_plus_shipping_method();

		// localize scripts
		wp_localize_script( 'wc-local-pickup-plus-frontend', 'wc_local_pickup_plus_frontend', [

			// Add any config/state properties here, for example:
			// 'is_user_logged_in' => is_user_logged_in()
			'ajax_url'                                     => admin_url( 'admin-ajax.php' ),
			'is_cart'                                      => is_cart(),
			'is_checkout'                                  => is_checkout(),
			'shipping_method_id'                           => $shipping_method->get_method_id(),
			'use_enhanced_search'                          => $shipping_method->is_enhanced_search_enabled(),
			'pickup_selection_mode'                        => $shipping_method->pickup_selection_mode(),
			'item_handling_mode'                           => $shipping_method->item_handling_mode(),
			'default_package_handling'                     => $shipping_method->get_default_handling(),
			'start_of_week'                                => get_option( 'start_of_week', 1 ),
			'ship_to_destination'                          => get_option( 'woocommerce_ship_to_destination' ),
			'date_format'                                  => $this->get_date_format(),
			'month_names'                                  => $this->get_month_names(),
			'day_initials'                                 => $this->get_day_initials(),
			'display_shipping_address_fields'              => $shipping_method->display_shipping_address_fields(),
			'apply_pickup_location_tax'                    => $shipping_method->apply_pickup_location_tax(),
			'pickup_appointments'                          => $shipping_method->pickup_appointments_mode(),
			'pickup_locations_lookup_nonce'                => wp_create_nonce( 'pickup-locations-lookup' ),
			'set_package_items_handling_nonce'             => wp_create_nonce( 'set-package-items-handling' ),
			'set_cart_item_handling_nonce'                 => wp_create_nonce( 'set-cart-item-handling' ),
			'set_package_handling_nonce'                   => wp_create_nonce( 'set-package-handling' ),
			'get_pickup_location_area_nonce'               => wp_create_nonce( 'get-pickup-location-area' ),
			'get_pickup_location_name_nonce'               => wp_create_nonce( 'get-pickup-location-name' ),
			'get_pickup_location_appointment_data_nonce'   => wp_create_nonce( 'get-pickup-location-appointment-data' ),
			'get_pickup_location_opening_hours_list_nonce' => wp_create_nonce( 'get-pickup-location-opening-hours-list' ),

			'i18n' => [

				// Add i18n strings here, for example:
				// 'local_pickup_plus' => __( 'Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' )
				'datepicker_button'              => __( 'Choose a pickup date', 'woocommerce-shipping-local-pickup-plus' ),
				'search_type_minimum_characters' => __( 'Enter a postcode or address...', 'woocommerce-shipping-local-pickup-plus' ),
				'search_error_loading'           => __( 'Error loading results', 'woocommerce-shipping-local-pickup-plus' ),
				'search_loading_more'            => __( 'Loading more results...', 'woocommerce-shipping-local-pickup-plus' ),
				'search_no_results'              => __( 'No results found', 'woocommerce-shipping-local-pickup-plus' ),
				'search_searching'               => __( 'Searching...', 'woocommerce-shipping-local-pickup-plus' ),

			],

		] );

		/**
		 * Upon enqueueing Local Pickup Plus frontend scripts.
		 *
		 * @since 2.0.0
		 *
		 * @param array $scripts handlers
		 * @param array $dependencies dependencies handles
		 */
		do_action( 'wc_local_pickup_plus_load_frontend_scripts', [ 'wc-local-pickup-plus-frontend' ], $dependencies );
	}


	/**
	 * Returns the date format to use for the front end datepicker.
	 *
	 * Converts the WordPress PHP date format to JS entities.
	 * Some entities aren't directly translatable so there might be some adaptation.
	 *
	 * Note: if planning to open this method or introduce a setting, consider moving it to the shipping method then. {FN 2018-09-07}
	 *
	 * @since 2.3.15
	 *
	 * @return string JS date format
	 */
	private function get_date_format() {

		$format     = wc_clean( wc_date_format() );
		$default    = 'yy-mm-dd';
		$separators = [ '.', '-', '/', '\\', ' ' ]; // accepted separators
		$php_to_js  = array_merge( [
			'j'  => 'd',         // day of the month, 1-2 digits without leading zeroes
			'd'  => 'dd',        // day of the month, 2 digits with leading zeroes
			'z'  => 'o',         // day of the year, 1-3 digits without leading zeroes
			'D'  => 'D',         // day name (short 3-letter abbreviation)
			'l'  => 'DD',        // day name (full name)
			'n'  => 'm',         // month number, 1-2 digits without leading zeroes
			'm'  => 'mm',        // month number, 2 digits with leading zeroes
			'M'  => 'M',         // month name (short 3-letter abbreviation)
			'F'  => 'MM',        // month name (full name)
			'y'  => 'y',         // year (2-digit abbreviation)
			'Y'  => 'yy',        // year (4-digit)
			'U'  => '@',         // UNIX timestamp
			'c'  => 'yy-mm-dd',  // ISO 8601 format (without time in JS)
			'r'  => 'D, d M yy', // RFC 2822 (without time in JS)
		], array_combine( $separators, $separators ) );

		// replace PHP entities with JS equivalent ones
		$format      = str_replace( array_keys( $php_to_js ), array_values( $php_to_js ), $format );
		// make sure resulting $format has only recognized characters by JS
		$unsupported = sprintf( '/[^%s]/u', preg_quote( implode( array_values( $php_to_js ) ), '/' ) );
		$format      = preg_replace( $unsupported, '', $format );

		// since some characters may have been removed, there might be some extra separators
		$previous    = false;
		$length      = strlen( $format );
		$date_format = '';

		for ( $i = 0; $i < $length; $i++ ) {

			$character = $format[ $i ];

			if ( $character !== $previous || ! in_array( $character, $separators, false ) ) {
				$date_format .= $character;
			}

			$previous = $character;
		}

		/**
		 * Filters the date format to be used in pickup appointments.
		 *
		 * @since 2.3.15
		 *
		 * @param string $format a valid JS date format as accepted by the jQuery DatePicker
		 */
		$date_format = wc_clean( (string) apply_filters( 'wc_local_pickup_plus_date_format', trim( $date_format ) ) );

		return empty( $date_format ) ? $default : $date_format;
	}


	/**
	 * Get localized month names.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of names ordered by month (1-12)
	 */
	private function get_month_names() {

		$month_names = [];

		// important reminder: in JavaScript month numbers range from 0 to 11 vs 1 to 12 in PHP
		for ( $i = 11; $i > -1; $i-- ) {

			$month_number = $i + 1;

			$month_names[ (string) $i ] = date_i18n( 'F', strtotime( "1980-{$month_number}-01" ) );
		}

		return $month_names;
	}


	/**
	 * Get localized day initial letters.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of day initials
	 */
	private function get_day_initials() {

		$day_initials = [];

		for ( $i = 0; $i < 7; $i++ ) {
			$day_initial    = date_i18n( 'D', strtotime( "Sunday + $i days" ) );
			$day_initials[] = $day_initial[0];
		}

		return $day_initials;
	}


}

class_alias( Frontend::class, 'WC_Local_Pickup_Plus_Frontend' );
