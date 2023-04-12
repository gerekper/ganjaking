<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * Aelia Currency Switcher
 * https://aelia.co/shop/currency-switcher-woocommerce/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_Aelia_Currency_Switcher {

	/**
	 * Flag to check in Aelia Currency Switcher is enabled
	 *
	 * @var int|null
	 */
	public $is_aelia_currency_switcher = false;
	/**
	 * Cache for the default currency
	 *
	 * @var int|null
	 */
	public $default_to_currency = false;
	/**
	 * Cache for the default from currency
	 *
	 * @var int|null
	 */
	public $default_from_currency = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Aelia_Currency_Switcher|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 6.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 6.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 1 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 6.0
	 */
	public function add_compatibility() {

		if ( ! $this->is_aelia_currency_switcher ) {
			return;
		}

		add_filter( 'wc_epo_enabled_currencies', [ $this, 'wc_epo_enabled_currencies' ], 10, 1 );
		add_filter( 'wc_epo_convert_to_currency', [ $this, 'wc_epo_convert_to_currency' ], 10, 3 );
		add_filter( 'wc_epo_get_current_currency_price', [ $this, 'wc_epo_get_current_currency_price' ], 10, 6 );
		add_filter( 'wc_epo_remove_current_currency_price', [ $this, 'wc_epo_remove_current_currency_price' ], 10, 9 );
		add_filter( 'wc_epo_get_currency_price', [ $this, 'tm_wc_epo_get_currency_price' ], 10, 7 );

	}

	/**
	 * Setup initial variables
	 *
	 * @since 6.0
	 */
	public function plugins_loaded() {

		$this->is_aelia_currency_switcher = class_exists( 'WC_Aelia_CurrencySwitcher' );
		$this->add_compatibility();

	}

	/**
	 * Alter enabled currencies
	 *
	 * @param array $currencies Array of currencies.
	 *
	 * @since 6.0
	 */
	public function wc_epo_enabled_currencies( $currencies = [] ) {

		$enabled_currencies = apply_filters( 'wc_aelia_cs_enabled_currencies', $currencies );

		return $enabled_currencies;

	}

	/**
	 * Get current currency price
	 *
	 * @param string     $price The price to convert.
	 * @param string     $type The option price type.
	 * @param array|null $currencies Array of currencies.
	 * @param boolean    $currency The currency to get the price.
	 * @param boolean    $product_price The product price (for precent price type).
	 * @param boolean    $tc_added_in_currency The currenct the product was added in.
	 *
	 * @since 6.0
	 */
	public function wc_epo_get_current_currency_price( $price = '', $type = '', $currencies = null, $currency = false, $product_price = false, $tc_added_in_currency = false ) {

		if ( is_array( $type ) ) {
			$type = '';
		}
		// Check if the price should be processed only once.
		if ( 'math' === $type ) {
			// Replaces any number between curly braces with the current currency.
			$price = preg_replace_callback(
				'/\{(\d+)\}/',
				function( $matches ) {
					return apply_filters( 'wc_epo_get_currency_price', $matches[1], false, '' );
				},
				$price
			);
		} elseif ( in_array( (string) $type, [ '', 'fixedcurrenttotal', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ], true ) ) {

			$price = $this->get_price_in_currency( $price, $currency, null, $currencies, $type );

		} elseif ( false !== $product_price && false !== $tc_added_in_currency && 'percent' === (string) $type ) {

			$product_price = $this->get_price_in_currency( $product_price, $tc_added_in_currency, null, $currencies, '' );
			$price         = $product_price * ( $price / 100 );

		}

		return $price;

	}

	/**
	 * Get current currency price
	 *
	 * @param string      $price The price to convert.
	 * @param boolean     $currency The currency to get the price.
	 * @param string      $price_type The option price type.
	 * @param boolean     $current_currency The current currency.
	 * @param array|null  $price_per_currencies Array of price per currency.
	 * @param string|null $key The option key.
	 * @param string|null $attribute The option attribute.
	 *
	 * @since 6.0
	 */
	public function tm_wc_epo_get_currency_price( $price = '', $currency = false, $price_type = '', $current_currency = false, $price_per_currencies = null, $key = null, $attribute = null ) {

		if ( ! $currency ) {
			return $this->wc_epo_get_current_currency_price( $price, $price_type, $currency );
		}
		$tc_get_default_currency = apply_filters( 'tc_get_default_currency', get_option( 'woocommerce_currency' ) );

		if ( $current_currency && $current_currency === $currency && $current_currency === $tc_get_default_currency ) {
			return $price;
		}

		$price = $this->get_price_in_currency( $price, $currency, null, $price_per_currencies, $price_type, $key, $attribute );

		return $price;

	}

	/**
	 * Remove current currency price
	 *
	 * @param mixed       $price The price to convert.
	 * @param string      $type The option price type.
	 * @param string      $to_currency The currency to convert to.
	 * @param string      $from_currency The currency to convert from.
	 * @param array|null  $currencies Array of currencies.
	 * @param string|null $key The option key.
	 * @param string|null $attribute The option attribute.
	 * @param array       $cart_item The cart item.
	 * @since 6.0
	 */
	public function wc_epo_remove_current_currency_price( $price = '', $type = '', $to_currency = null, $from_currency = null, $currencies = null, $key = null, $attribute = null, $cart_item = [] ) {

		$price = $this->get_price_in_currency( $price, $to_currency, $from_currency, $currencies, $type, $key, $attribute );

		return $price;
	}

	/**
	 * Convert to currency
	 *
	 * @param string       $price The price to convert.
	 * @param string|false $from_currency The currency to convert from.
	 * @param string|false $to_currency The currency to convert to.
	 *
	 * @since 6.0
	 */
	public function wc_epo_convert_to_currency( $price = '', $from_currency = false, $to_currency = false ) {

		if ( ! $from_currency || ! $to_currency || $from_currency === $to_currency ) {
			return $price;
		}

		// todo: if needed extend this as the whole method is only used for fixed conversions.
		$price = $this->get_price_in_currency( $price, $to_currency, $from_currency );

		return $price;

	}

	/**
	 * Helper function
	 *
	 * @see get_price_in_currency
	 */
	public function get_default_from_currency() {
		if ( ! $this->default_from_currency ) {
			$this->default_from_currency = get_option( 'woocommerce_currency' );
		}

		return $this->default_from_currency;
	}

	/**
	 * Helper function
	 *
	 * @see get_price_in_currency
	 */
	public function get_default_to_currency() {
		if ( ! $this->default_to_currency ) {
			$this->default_to_currency = themecomplete_get_woocommerce_currency();
		}

		return $this->default_to_currency;
	}


	/**
	 * Basic integration with WooCommerce Currency Switcher, developed by Aelia
	 * (http://aelia.co). This method can be used by any 3rd party plugin to
	 * return prices converted to the active currency.
	 *
	 * @param double $price The source price.
	 * @param string $to_currency The target currency. If empty, the active currency
	 *               will be taken.
	 * @param string $from_currency The source currency. If empty, WooCommerce base
	 *               currency will be taken.
	 * @param array  $currencies Array of currencies.
	 * @param string $type The price type.
	 * @param string $key The option key.
	 * @param string $attribute The option attribute.
	 * @return double The price converted from source to destination currency.
	 * @author Aelia <support@aelia.co>
	 * @link   http://aelia.co
	 */
	protected function get_price_in_currency( $price, $to_currency = null, $from_currency = null, $currencies = null, $type = null, $key = null, $attribute = null ) {

		if ( empty( $from_currency ) ) {
			$from_currency = $this->get_default_from_currency();
		}
		if ( empty( $to_currency ) ) {
			$to_currency = $this->get_default_to_currency();
		}
		if ( $from_currency === $to_currency ) {
			return $price;
		}
		if ( null !== $type && in_array( (string) $type, [ '', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ], true ) && is_array( $currencies ) && isset( $currencies[ $to_currency ] ) ) {
			$v = $currencies[ $to_currency ];
			if ( null !== $key && isset( $v[ $key ] ) ) {
				$v = $v[ $key ];
			}
			if ( is_array( $v ) ) {
				$v = array_values( $v );
				$v = $v[0];
				if ( is_array( $v ) ) {
					$v = array_values( $v );
					$v = $v[0];
				}
			}

			if ( '' !== $v ) {
				return $v;
			}
		}

		return apply_filters( 'wc_epo_cs_convert', apply_filters( 'wc_aelia_cs_convert', $price, $from_currency, $to_currency, 10 ), $from_currency, $to_currency );
	}

}
