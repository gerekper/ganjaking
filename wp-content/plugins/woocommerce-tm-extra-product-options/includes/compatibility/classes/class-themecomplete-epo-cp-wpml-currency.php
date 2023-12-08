<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WPML Currency
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_WPML_Currency {

	/**
	 * If WPML is active
	 *
	 * @var boolean
	 */
	public $is_wpml = false;
	/**
	 * Flag to check in WPML Multi Currency is enabled
	 *
	 * @var integer|null|boolean
	 */
	public $is_wpml_multi_currency = false;
	/**
	 * Cache for the default currency
	 *
	 * @var string|null|boolean
	 */
	public $default_to_currency = false;
	/**
	 * Cache for the default from currency
	 *
	 * @var string|null|boolean
	 */
	public $default_from_currency = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_WPML_Currency|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_WPML_Currency
	 * @since 1.0
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
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 1 );
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded_standalone' ], 10001 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! $this->is_wpml_multi_currency ) {
			return;
		}

		add_filter( 'wc_epo_enabled_currencies', [ $this, 'wc_epo_enabled_currencies' ], 10, 1 );
		add_filter( 'wc_epo_convert_to_currency', [ $this, 'wc_epo_convert_to_currency' ], 10, 3 );
		add_filter( 'wc_epo_get_current_currency_price', [ $this, 'wc_epo_get_current_currency_price' ], 10, 6 );
		add_filter( 'wc_epo_remove_current_currency_price', [ $this, 'wc_epo_remove_current_currency_price' ], 10, 6 );
		add_filter( 'wc_epo_get_currency_price', [ $this, 'tm_wc_epo_get_currency_price' ], 10, 6 );
	}

	/**
	 * Setup initial variables
	 *
	 * @return void
	 * @since 6.0
	 */
	public function plugins_loaded() {
		$this->is_wpml                = THEMECOMPLETE_EPO_WPML()->is_active();
		$this->is_wpml_multi_currency = THEMECOMPLETE_EPO_WPML()->is_multi_currency();

		$this->add_compatibility();
	}

	/**
	 * Setup initial variables as a standalone plugin
	 *
	 * @return void
	 * @since 6.0
	 */
	public function plugins_loaded_standalone() {
		// Exit if already defined from priority 1.
		if ( $this->is_wpml_multi_currency ) {
			return;
		}

		$this->is_wpml                = THEMECOMPLETE_EPO_WPML()->is_active();
		$this->is_wpml_multi_currency = THEMECOMPLETE_EPO_WPML()->is_multi_currency();

		$this->add_compatibility();
	}

	/**
	 * Alter enabled currencies
	 *
	 * @param array<mixed> $currencies Array of currencies.
	 * @return array<mixed>
	 * @since 6.4
	 */
	public function wc_epo_enabled_currencies( $currencies = [] ) {
		global $woocommerce_wpml;
		if ( $woocommerce_wpml ) {
			if ( $this->is_wpml_multi_currency ) {
				$currencies = array_keys( $woocommerce_wpml->settings['currency_options'] );
			}
		}

		return $currencies;
	}

	/**
	 * Get current currency price
	 *
	 * @param string            $price The price to convert.
	 * @param mixed             $type The option price type.
	 * @param array<mixed>|null $currencies Array of currencies.
	 * @param string|false      $currency The currency to get the price.
	 * @param mixed             $product_price The product price (for percent price type).
	 * @param string|false      $tc_added_in_currency The current the product was added in.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function wc_epo_get_current_currency_price( $price = '', $type = '', $currencies = null, $currency = false, $product_price = false, $tc_added_in_currency = false ) {
		global $woocommerce_wpml;
		if ( is_array( $type ) ) {
			$type = '';
		}
		// Check if the price should be processed only once.
		if ( 'math' === $type ) {
			// Replaces any number between curly braces with the current currency.
			$price = preg_replace_callback(
				'/\{(\d+)\}/',
				function ( $matches ) {
					return apply_filters( 'wc_epo_get_currency_price', $matches[1], false, '' );
				},
				$price
			);
		} elseif ( in_array( (string) $type, [ '', 'fixedcurrenttotal', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ], true ) ) {

			if ( $this->is_wpml_multi_currency ) {

				if ( is_callable( [ $woocommerce_wpml->multi_currency, 'convert_price_amount' ] ) ) {
					$price = $woocommerce_wpml->multi_currency->convert_price_amount( $price, $currency );
				} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( [ $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ] ) ) {
					$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $currency );
				}
			} else {

				$price = $this->get_price_in_currency( $price, $currency, null, $currencies, $type );

			}
		} elseif ( false !== $product_price && false !== $tc_added_in_currency && 'percent' === (string) $type ) {

			if ( $this->is_wpml_multi_currency ) {

				if ( is_callable( [ $woocommerce_wpml->multi_currency, 'convert_price_amount' ] ) ) {
					$product_price = $woocommerce_wpml->multi_currency->convert_price_amount( $product_price, $tc_added_in_currency );
				} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( [ $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ] ) ) {
					$product_price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $product_price, $tc_added_in_currency );
				}

				$price = floatval( $product_price ) * ( floatval( $price ) / 100 );

			} else {

				$product_price = $this->get_price_in_currency( $product_price, $tc_added_in_currency, null, $currencies, '' );
				$price         = floatval( $product_price ) * ( floatval( $price ) / 100 );

			}
		}

		return $price;
	}

	/**
	 * Get current currency price
	 *
	 * @param string            $price The price to convert.
	 * @param string|false      $currency The currency to get the price.
	 * @param string            $price_type The option price type.
	 * @param string|false      $current_currency The current currency.
	 * @param array<mixed>|null $price_per_currencies Array of price per currency.
	 * @param string|null       $key The option key.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function tm_wc_epo_get_currency_price( $price = '', $currency = false, $price_type = '', $current_currency = false, $price_per_currencies = null, $key = null ) {
		if ( ! $currency ) {
			return $this->wc_epo_get_current_currency_price( $price, $price_type );
		}
		$tc_get_default_currency = apply_filters( 'tc_get_default_currency', get_option( 'woocommerce_currency' ) );

		if ( $current_currency && $current_currency === $currency && $current_currency === $tc_get_default_currency ) {
			return $price;
		}
		if ( $this->is_wpml_multi_currency ) {
			// todo:doesn't work at the moment.
			$price = apply_filters( 'wcml_raw_price_amount', $price, $currency );

		} else {

			$price = $this->get_price_in_currency( $price, $currency, null, $price_per_currencies, $price_type, $key );

		}

		return $price;
	}

	/**
	 * Remove current currency price
	 *
	 * @param string            $price The price to convert.
	 * @param string            $type The option price type.
	 * @param string            $to_currency The currency to convert to.
	 * @param string            $from_currency The currency to convert from.
	 * @param array<mixed>|null $currencies Array of currencies.
	 * @param string|null       $key The option key.
	 * @return mixed
	 * @since 6.0
	 */
	public function wc_epo_remove_current_currency_price( $price = '', $type = '', $to_currency = null, $from_currency = null, $currencies = null, $key = null ) {

		if ( $this->is_wpml_multi_currency ) {
			global $woocommerce_wpml;
			if ( is_callable( [ $woocommerce_wpml->multi_currency, 'unconvert_price_amount' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->unconvert_price_amount( $price );
			} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( [ $woocommerce_wpml->multi_currency->prices, 'unconvert_price_amount' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $price );
			}
		} else {
			$price = $this->get_price_in_currency( $price, $to_currency, $from_currency, $currencies, $type, $key );
		}

		return $price;
	}

	/**
	 * Convert to currency
	 *
	 * @param string       $price The price to convert.
	 * @param string|false $from_currency The currency to convert from.
	 * @param string|false $to_currency The currency to convert to.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function wc_epo_convert_to_currency( $price = '', $from_currency = false, $to_currency = false ) {
		if ( ! $from_currency || ! $to_currency || $from_currency === $to_currency ) {
			return $price;
		}

		if ( $this->is_wpml_multi_currency ) {
			// todo: find a way to get correct price for any $from_currency as
			// currently it defaults to get_option( 'woocommerce_currency' ).
			global $woocommerce_wpml;
			if ( is_callable( [ $woocommerce_wpml->multi_currency, 'convert_price_amount_by_currencies' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->convert_price_amount_by_currencies( $price, $from_currency, $to_currency );
			} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( [ $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ] ) ) {
				$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $from_currency, $to_currency );
			}
		} else {
			// todo: if needed extend this as the whole method is only used for fixed conversions.
			$price = $this->get_price_in_currency( $price, $to_currency, $from_currency );

		}

		return $price;
	}

	/**
	 * Helper function
	 *
	 * @return string
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
	 * @return string
	 * @see get_price_in_currency
	 */
	public function get_default_to_currency() {
		if ( ! $this->default_to_currency ) {
			$this->default_to_currency = themecomplete_get_woocommerce_currency();
		}

		return $this->default_to_currency;
	}

	/**
	 * Return prices converted to the active currency
	 *
	 * @param mixed        $price The source price.
	 * @param string       $to_currency The target currency. If empty, the active currency
	 *                     will be taken.
	 * @param string       $from_currency The source currency. If empty, WooCommerce base
	 *                     currency will be taken.
	 * @param array<mixed> $currencies Array of currencies.
	 * @param string       $type The price type.
	 * @param string       $key The option key.
	 *
	 * @return mixed The price converted from source to destination currency.
	 */
	protected function get_price_in_currency( $price, $to_currency = null, $from_currency = null, $currencies = null, $type = null, $key = null ) {

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

		return apply_filters( 'wc_epo_cs_convert', $price, $from_currency, $to_currency );
	}
}
