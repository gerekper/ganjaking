<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * 1. Aelia Currency Switcher 
 * https://aelia.co/shop/currency-switcher-woocommerce/
 * 2. WooCommerce Currency Switcher from realmag777 
 * https://codecanyon.net/item/woocommerce-currency-switcher/8085217
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_currency {

	public $is_aelia_currency_switcher = FALSE;
	public $is_woocs = FALSE;
	public $is_all_in_one_cc = FALSE;
	public $is_wpml = FALSE;
	public $is_wpml_multi_currency = FALSE;
	public $default_to_currency = FALSE;
	public $default_from_currency = FALSE;

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wc_epo_add_compatibility', array( $this, 'add_compatibility' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 1 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		add_filter( 'wc_epo_enabled_currencies', array( $this, 'wc_epo_enabled_currencies' ), 10, 1 );

		add_filter( 'wc_epo_convert_to_currency', array( $this, 'wc_epo_convert_to_currency' ), 10, 3 );

		add_filter( 'wc_epo_product_price', array( $this, 'wc_epo_product_price' ), 10, 3 );

		add_filter( 'wc_epo_product_price_correction', array( $this, 'wc_epo_product_price_correction' ), 10, 2 );
		add_filter( 'wc_epo_option_price_correction', array( $this, 'wc_epo_option_price_correction' ), 10, 3 );

		add_filter( 'woocs_fixed_raw_woocommerce_price', array( $this, 'woocs_fixed_raw_woocommerce_price' ), 10, 3 );

		add_filter( 'wc_epo_get_current_currency_price', array( $this, 'wc_epo_get_current_currency_price' ), 10, 7 );
		add_filter( 'wc_epo_remove_current_currency_price', array( $this, 'wc_epo_remove_current_currency_price' ), 10, 8 );

		add_filter( 'wc_epo_get_currency_price', array( $this, 'tm_wc_epo_get_currency_price' ), 10, 8 );
		add_filter( 'woocommerce_tm_epo_price_add_on_cart', array( $this, 'tm_epo_price_add_on_cart' ), 10, 2 );

		add_filter( 'woocommerce_tm_epo_price_per_currency_diff', array( $this, 'tm_epo_price_per_currency_diff' ), 10, 2 );

		add_filter( 'wc_epo_get_price_html', array( $this, 'wc_epo_get_price_html' ), 10, 2 );

		add_filter( 'wc_epo_cart_set_price', array( $this, 'wc_epo_cart_set_price' ), 10, 2 );

		add_filter( 'wc_epo_cs_convert', array( $this, 'wc_epo_cs_convert' ), 10, 3 );

		add_action( 'wc_epo_currency_actions', array( $this, 'wc_epo_currency_actions' ), 10, 3 );

		add_filter( 'wc_epo_script_args', array( $this, 'wc_epo_script_args' ), 10, 1 );

	}

	/**
	 * Setup initial variables
	 *
	 * @since 1.0
	 */
	public function plugins_loaded() {

		$this->is_aelia_currency_switcher = class_exists( 'WC_Aelia_CurrencySwitcher' );
		$this->is_woocs                   = class_exists( 'WOOCS' );
		$this->is_all_in_one_cc           = class_exists( 'WooCommerce_All_in_One_Currency_Converter_Main' );

		$this->is_wpml                = THEMECOMPLETE_EPO_WPML()->is_active();
		$this->is_wpml_multi_currency = THEMECOMPLETE_EPO_WPML()->is_multi_currency();

	}

	/**
	 * Alter enabled currencies
	 *
	 * @since 1.0
	 */
	public function wc_epo_enabled_currencies( $currencies = array() ) {

		$enabled_currencies = apply_filters( 'wc_aelia_cs_enabled_currencies', $currencies );

		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$currencies = is_callable( array( $WOOCS, 'get_currencies' ) ) ? $WOOCS->get_currencies() : array();

			if ( $currencies && is_array( $currencies ) ) {
				$enabled_currencies = array();
				foreach ( $currencies as $key => $value ) {
					$enabled_currencies[] = $value['name'];
				}
			}
		} elseif ( class_exists( 'WooCommerce_All_in_One_Currency_Converter_Main' ) ) {
			global $woocommerce_all_in_one_currency_converter;
			$currency_data = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
			if ( $currency_data && is_array( $currency_data ) ) {
				$enabled_currencies = array();
				foreach ( $currency_data as $key => $value ) {
					$enabled_currencies[] = $key;
				}
			}
		}

		return $enabled_currencies;

	}

	/**
	 * Add to main JS script arguments
	 *
	 * @since 1.0
	 */
	public function wc_epo_script_args( $args ) {

		if ( $this->is_woocs && isset( $args['product_id'] ) ) {
			$customer_price_format = get_option( 'woocs_customer_price_format', '' );

			if ( ! empty( $customer_price_format ) ) {
				global $WOOCS;
				$args["customer_price_format"]            = $customer_price_format;
				$args["current_currency"]                 = $WOOCS->current_currency;
				$args["customer_price_format_wrap_start"] = '<span class="woocs_price_code" data-product-id="' . $args['product_id'] . '">';
				$args["customer_price_format_wrap_end"]   = '</span>';
			}
		}

		return $args;

	}

	/**
	 * Alter prodcut variables in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_currency_actions( $price1, $price2, $cart_item ) {

		$cart_item['data']->tc_price1                     = floatval( $price1 );//option prices
		$cart_item['data']->tc_price2                     = floatval( $price2 );
		$cart_item['data']->tm_epo_product_original_price = floatval( $cart_item['tm_epo_product_original_price'] );

	}

	/**
	 * Fixed currency support for WOOCS
	 *
	 * @since 1.0
	 */
	public function woocs_fixed_raw_woocommerce_price( $fixed_price = 0, $product = FALSE, $main_price = NULL ) {

		if ( $main_price === NULL ) {
			if ( ! defined( 'THEMECOMPLETE_CS_ERROR' ) ) {
				define( 'THEMECOMPLETE_CS_ERROR', 1 );
				wc_add_notice( "You are using an unsupported version of Currency switcher. Prices will not be correct!", 'error' );
			}

			return $fixed_price;
		}
		global $WOOCS;
		$product_id = $product->get_id();


		$flag = FALSE;
		if ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == "yes" ) {
			$flag = TRUE;
		} elseif ( THEMECOMPLETE_EPO()->tm_epo_global_override_product_price == "" ) {
			$tm_meta_cpf = themecomplete_get_post_meta( $product_id, 'tm_meta_cpf', TRUE );
			if ( ! is_array( $tm_meta_cpf ) ) {
				$tm_meta_cpf = array();
			}

			if ( ! empty( $tm_meta_cpf['price_override'] ) ) {
				$flag = TRUE;
			}
		}
		if ( $flag ) {
			return $WOOCS->woocs_exchange_value( $main_price );
		}

		$type = $WOOCS->fixed->get_price_type( $product, $main_price );

		$get_value = get_post_meta( $product_id, '_' . $type . '_price', TRUE );
		$get_value = floatval( $get_value );

		if ( floatval( $main_price ) == $get_value ) {
			return $fixed_price;
		}

		if ( THEMECOMPLETE_EPO()->wc_vars["is_cart"] || THEMECOMPLETE_EPO()->wc_vars["is_checkout"] ) {

			$option_prices                      = floatval( $WOOCS->woocs_exchange_value( $product->tc_price1 ) );
			$original_price_in_current_currency = floatval( $product->tm_epo_product_original_price );

			$new_price = $original_price_in_current_currency + $option_prices;

			if ( $new_price < 0 ) {
				return $fixed_price;
			}

			$fixed_price = $new_price;

			return $fixed_price;
		}

		return $fixed_price;

	}

	/**
	 * Set price in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_cart_set_price( $cart_item, $price ) {
		if ( $this->is_aelia_currency_switcher ) {
			if ( ! property_exists( 'WC_Aelia_CurrencySwitcher', 'version' ) || ( property_exists( 'WC_Aelia_CurrencySwitcher', 'version' ) && version_compare( WC_Aelia_CurrencySwitcher::$version, '4.4.7', '<' ) ) ) {
				$cart_item['data']->set_price( $price );
			}
		}

		return $cart_item;
	}

	/**
	 * Add additional info in price html
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_price_html( $price_html, $product ) {

		if ( $this->is_woocs ) {
			global $WOOCS;

			$currencies = is_callable( array( $WOOCS, 'get_currencies' ) ) ? $WOOCS->get_currencies() : array();

			$customer_price_format = get_option( 'woocs_customer_price_format', '' );

			if ( ! empty( $customer_price_format ) ) {
				$txt        = '<span class="woocs_price_code" data-product-id="' . themecomplete_get_id( $product ) . '">' . $customer_price_format . '</span>';
				$txt        = str_replace( '__PRICE__', $price_html, $txt );
				$price_html = str_replace( '__CODE__', $WOOCS->current_currency, $txt );
			}


			// Hide cents on front as html element
			if ( ! in_array( $WOOCS->current_currency, $WOOCS->no_cents ) ) {
				if ( $currencies[ $WOOCS->current_currency ]['hide_cents'] == 1 ) {
					$price_html = preg_replace( '/\.[0-9][0-9]/', '', $price_html );
				}
			}

			if ( ( get_option( 'woocs_price_info', 0 ) AND ! is_admin() ) OR isset( $_REQUEST['get_product_price_by_ajax'] ) ) {


				$info             = "<ul>";
				$current_currency = $WOOCS->current_currency;
				foreach ( $currencies as $curr ) {
					if ( ! isset( $curr['name'] ) || $curr['name'] == $current_currency ) {
						continue;
					}
					$WOOCS->current_currency = $curr['name'];
					$value                   = $product->get_price() * $currencies[ $curr['name'] ]['rate'];
					$value                   = number_format( $value, 2, $WOOCS->decimal_sep, '' );
					if ( themecomplete_get_product_type( $product ) != 'variable' ) {
						$info .= "<li><b>" . $curr['name'] . "</b>: " . $WOOCS->wc_price( $value, FALSE, array( 'currency' => $curr['name'] ) ) . "</li>";
					} else {

						if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '>=' ) ) {
							$min_value = $product->get_variation_price( 'min', TRUE ) * $currencies[ $curr['name'] ]['rate'];
							$max_value = $product->get_variation_price( 'max', TRUE ) * $currencies[ $curr['name'] ]['rate'];
						} else {
							$min_value = $product->min_variation_price * $currencies[ $curr['name'] ]['rate'];
							$max_value = $product->max_variation_price * $currencies[ $curr['name'] ]['rate'];
						}

						$min_value = number_format( $min_value, 2, $WOOCS->decimal_sep, '' );

						$max_value = number_format( $max_value, 2, $WOOCS->decimal_sep, '' );

						$var_price = $WOOCS->wc_price( $min_value, array( 'currency' => $curr['name'] ) );
						$var_price .= ' - ';
						$var_price .= $WOOCS->wc_price( $max_value, array( 'currency' => $curr['name'] ) );
						$info      .= "<li><b>" . $curr['name'] . "</b>: " . $var_price . "</li>";
					}
				}
				$WOOCS->current_currency = $current_currency;
				$info                    .= "</ul>";
				$info                    = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
				$price_html              .= $info;
			}
		}

		return $price_html;

	}

	/**
	 * Check WOOCS version for meta mode
	 * (THIS WILL BE REMOVED IN THE FUTURE WHEN SUPPORT FOR VERSION 1X WILL BE DROPPED)
	 *
	 * @since 1.0
	 */
	private function _get_woos_price_calculation() {

		$oldway = FALSE;
		if ( $this->is_woocs ) {
			global $WOOCS;
			if ( property_exists( $WOOCS, 'the_plugin_version' ) || defined( 'WOOCS_VERSION' ) ) {
				$vi = property_exists( $WOOCS, 'the_plugin_version' ) ? $WOOCS->the_plugin_version : ( defined( 'WOOCS_VERSION' ) ? WOOCS_VERSION : FALSE );
				$v  = intval( $vi );
				if ( $vi !== FALSE ) {
					if ( $v == 1 ) {
						if ( version_compare( $vi, '1.0.9', '<' ) ) {
							$oldway = TRUE;
						}
					} else {
						if ( version_compare( $vi, '2.0.9', '<' ) ) {
							$oldway = TRUE;
						}
					}
				}
			}
		}

		return $oldway;

	}

	/**
	 * Get product price
	 * This filter is currently only used for product prices.
	 *
	 * @since 1.0
	 */
	public function wc_epo_product_price( $price = "", $type = "", $is_meta_value = TRUE, $currency = FALSE ) {
		if ( $this->is_woocs ) {
			global $WOOCS;
			if ( property_exists( $WOOCS, 'the_plugin_version' ) || defined( 'WOOCS_VERSION' ) ) {
				if ( ! $is_meta_value && ! $this->_get_woos_price_calculation() ) {
					if ( $WOOCS->is_multiple_allowed ) {
						// no converting needed
					} else {
						$price = apply_filters( 'woocs_exchange_value', $price );
					}
				} else {
					$currencies = is_callable( array( $WOOCS, 'get_currencies' ) ) ? $WOOCS->get_currencies() : array();
					if ( ! $currency ) {
						$current_currency = $WOOCS->current_currency;
					} else {
						$current_currency = $currency;
					}
					if ( isset( $currencies[ $current_currency ] ) && isset( $currencies[ $current_currency ]['rate'] ) ) {
						$price = (double) $price * (double) $currencies[ $current_currency ]['rate'];
					}
				}
			}
		} elseif ( $this->is_all_in_one_cc ) {
			global $woocommerce_all_in_one_currency_converter;
			$user_currency     = $woocommerce_all_in_one_currency_converter->settings->session_currency;
			$currency_data     = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
			$conversion_method = $woocommerce_all_in_one_currency_converter->settings->get_conversion_method();

			if ( ! $currency ) {
				$current_currency = $user_currency;
			} else {
				$current_currency = $currency;
			}
			if ( isset( $currency_data[ $current_currency ] ) && isset( $currency_data[ $current_currency ]['rate'] ) ) {
				$price = (double) $price * (double) $currency_data[ $current_currency ]['rate'];
			}
		}

		return $price;
	}

	/**
	 * Adjusts option prices when using different currency price for versions > 2.0.9
	 * MUST BE USED ONLY WHEN IT IS KNOWN THAT THE PRICE IS DIFFERENT !
	 *
	 * @since 1.0
	 */
	public function tm_epo_price_per_currency_diff( $price = 0, $to_currency = NULL ) {
		if ( $this->is_woocs && ! $this->_get_woos_price_calculation() ) {
			global $WOOCS;
			if ( $to_currency === NULL || ( $to_currency !== NULL && $WOOCS->default_currency == $to_currency ) ) {
				$price = $this->wc_epo_remove_current_currency_price( $price );
			}
		}

		return $price;
	}

	/**
	 * Alter product price in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_product_price_correction( $price, $cart_item ) {
		if ( $this->is_woocs || $this->is_all_in_one_cc ) {
			global $WOOCS;

			if ( $WOOCS->is_multiple_allowed ) {
				$is_fixed_price = - 1;
				if ( $WOOCS->fixed ) {
					if ( in_array( $WOOCS->current_currency, $WOOCS->no_cents ) ) {
						$precision = 0;
					} else {
						if ( $WOOCS->current_currency != $WOOCS->default_currency ) {
							$precision = $WOOCS->get_currency_price_num_decimals( $WOOCS->current_currency, $WOOCS->price_num_decimals );
						} else {
							$precision = $WOOCS->get_currency_price_num_decimals( $WOOCS->default_currency, $WOOCS->price_num_decimals );
						}
					}

					if ( $cart_item['data']->is_type( 'variation' ) ) {
						$is_fixed_price = $WOOCS->_get_product_fixed_price( $cart_item['data'], "variation", $price, $precision );
					} else {
						$is_fixed_price = $WOOCS->_get_product_fixed_price( $cart_item['data'], "single", $price, $precision );
					}
				}
				if ( $is_fixed_price == - 1 ) {
					return apply_filters( 'wc_epo_remove_current_currency_price', $price );
				}
			}

		}

		return $price;
	}

	/**
	 * Alter option prices in cart
	 *
	 * @since 1.0
	 */
	public function wc_epo_option_price_correction( $price ) {
		if ( $this->is_woocs || $this->is_all_in_one_cc ) {
			return apply_filters( 'wc_epo_remove_current_currency_price', $price );
		}

		return $price;
	}

	/**
	 * Get current currency price
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_current_currency_price( $price = "", $type = "", $is_meta_value = TRUE, $currencies = NULL, $currency = FALSE, $product_price = FALSE, $tc_added_in_currency = FALSE ) {
		global $woocommerce_wpml;
		if ( is_array( $type ) ) {
			$type = "";
		}
		// Check if the price should be processed only once
		if ( in_array( (string) $type, array( '', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ) ) ) {// 'percentcurrenttotal',

			if ( $this->is_wpml_multi_currency ) {

				if ( is_callable( array( $woocommerce_wpml->multi_currency, 'convert_price_amount' ) ) ) {
					$price = $woocommerce_wpml->multi_currency->convert_price_amount( $price, $currency );
				} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( array( $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ) ) ) {
					$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $currency );
				}

			} elseif ( $this->is_woocs || $this->is_all_in_one_cc ) {				
				global $WOOCS;
				if ( is_array( $currencies ) && isset( $currencies[ $WOOCS->current_currency ] ) ) {
					$price = $currencies[ $WOOCS->current_currency ];
				} else {
					$price = $this->wc_epo_product_price( $price, $type, $is_meta_value );
				}

			} else {

				$price = $this->get_price_in_currency( $price, $currency, NULL, $currencies, $type );

			}

		} elseif ( $product_price !== FALSE && $tc_added_in_currency !== FALSE && (string) $type == 'percent' ) {

			if ( $this->is_wpml_multi_currency ) {

				if ( is_callable( array( $woocommerce_wpml->multi_currency, 'convert_price_amount' ) ) ) {
					$product_price = $woocommerce_wpml->multi_currency->convert_price_amount( $product_price, $tc_added_in_currency );
				} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( array( $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ) ) ) {
					$product_price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $product_price, $tc_added_in_currency );
				}

				$price = $product_price * ( $price / 100 );

			} elseif ( $this->is_woocs || $this->is_all_in_one_cc ) {
				global $WOOCS;
				if ( is_array( $currencies ) && isset( $currencies[ $WOOCS->current_currency ] ) ) {
					$product_price = $currencies[ $WOOCS->current_currency ];
				} else {
					$product_price = $this->wc_epo_product_price( $product_price, "", $is_meta_value, $tc_added_in_currency );
				}
				$price = $product_price * ( $price / 100 );

			} else {

				$product_price = $this->get_price_in_currency( $product_price, $tc_added_in_currency, NULL, $currencies, "" );
				$price         = $product_price * ( $price / 100 );

			}

		}

		return $price;

	}

	/**
	 * Get current currency price
	 *
	 * @since 1.0
	 */
	public function tm_wc_epo_get_currency_price( $price = "", $currency = FALSE, $price_type = "", $is_meta_value = TRUE, $current_currency = FALSE, $price_per_currencies = NULL, $key = NULL, $attribute = NULL ) {

		if ( ! $currency ) {
			return $this->wc_epo_get_current_currency_price( $price, $price_type, $is_meta_value, NULL, $currency );
		}
		if ( $current_currency && $current_currency == $currency ) {
			return $price;
		}

		if ( $this->is_wpml_multi_currency ) {
			// todo:doesn't work at the moment
			$price = apply_filters( 'wcml_raw_price_amount', $price, $currency );

		} elseif ( $this->is_woocs || $this->is_all_in_one_cc ) {

			$price = $this->wc_epo_product_price( $price, $price_type, $is_meta_value, $currency );

		} else {

			$price = $this->get_price_in_currency( $price, $currency, NULL, $price_per_currencies, $price_type, $key, $attribute );

		}

		return $price;

	}

	/**
	 * Remove current currency price
	 *
	 * @since 1.0
	 */
	public function wc_epo_remove_current_currency_price( $price = "", $type = "", $to_currency = NULL, $from_currency = NULL, $currencies = NULL, $key = NULL, $attribute = NULL ) {

		if ( $this->is_woocs ) {
			global $WOOCS;
			$currencies       = is_callable( array( $WOOCS, 'get_currencies' ) ) ? $WOOCS->get_currencies() : array();
			$current_currency = $WOOCS->current_currency;
			if ( ! empty( $currencies[ $current_currency ]['rate'] ) ) {
				$price = (double) $price / $currencies[ $current_currency ]['rate'];
			}
		} elseif ( $this->is_all_in_one_cc ) {
			global $woocommerce_all_in_one_currency_converter;
			$user_currency     = $woocommerce_all_in_one_currency_converter->settings->session_currency;
			$currency_data     = $woocommerce_all_in_one_currency_converter->settings->get_currency_data();
			$conversion_method = $woocommerce_all_in_one_currency_converter->settings->get_conversion_method();

			$current_currency = $user_currency;
			if ( isset( $currency_data[ $current_currency ] ) && ! empty( $currency_data[ $current_currency ]['rate'] ) ) {
				$price = (double) $price / (double) $currency_data[ $current_currency ]['rate'];
			}
		} elseif ( $this->is_wpml_multi_currency ) {
			global $woocommerce_wpml;
			if ( is_callable( array( $woocommerce_wpml->multi_currency, 'unconvert_price_amount' ) ) ) {
				$price = $woocommerce_wpml->multi_currency->unconvert_price_amount( $price );
			} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( array( $woocommerce_wpml->multi_currency->prices, 'unconvert_price_amount' ) ) ) {
				$price = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $price );
			}
		} else {
			$price = $this->get_price_in_currency( $price, $to_currency, $from_currency, $currencies, $type, $key, $attribute );
		}

		return $price;
	}

	/**
	 * Convert to currency
	 *
	 * @since 1.0
	 */
	public function wc_epo_convert_to_currency( $price = "", $from_currency = FALSE, $to_currency = FALSE ) {

		if ( ! $from_currency || ! $to_currency || $from_currency == $to_currency ) {
			return $price;
		}

		if ( $this->is_wpml_multi_currency ) {
			// todo: find a way to get correct price for any $from_currency as 
			// currently it defaults to get_option( 'woocommerce_currency' )
			global $woocommerce_wpml;
			if ( is_callable( array( $woocommerce_wpml->multi_currency, 'convert_price_amount' ) ) ) {
				$price = $woocommerce_wpml->multi_currency->convert_price_amount( $price, $to_currency );
			} elseif ( property_exists( $woocommerce_wpml->multi_currency, 'prices' ) && is_callable( array( $woocommerce_wpml->multi_currency->prices, 'convert_price_amount' ) ) ) {
				$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $price, $to_currency );
			}

		} elseif ( $this->is_woocs ) {
			global $WOOCS;
			$currencies       = is_callable( array( $WOOCS, 'get_currencies' ) ) ? $WOOCS->get_currencies() : array();
			$current_currency = $from_currency;
			if ( ! empty( $currencies[ $to_currency ]['rate'] ) && ! empty( $currencies[ $from_currency ]['rate'] ) ) {
				$price = (double) $price * ( $currencies[ $to_currency ]['rate'] / $currencies[ $from_currency ]['rate'] );
			}

		} else {
			// todo: if needed extend this as the whole method is only used for fixed conversions 
			$price = $this->get_price_in_currency( $price, $to_currency, $from_currency );

		}

		return $price;

	}

	/**
	 * Helper function
	 *
	 * @see get_price_in_currency
	 */
	public function get_default_from_currency(){
	    if (!$this->default_from_currency) {
	        $this->default_from_currency = get_option( 'woocommerce_currency' );
	    }

	    return $this->default_from_currency;
	}

	/**
	 * Helper function
	 *
	 * @see get_price_in_currency
	 */
	public function get_default_to_currency(){
	    if (!$this->default_to_currency) {
	        $this->default_to_currency = themecomplete_get_woocommerce_currency();
	    }

	    return $this->default_to_currency;
	}


	/**
	 * Basic integration with WooCommerce Currency Switcher, developed by Aelia
	 * (http://aelia.co). This method can be used by any 3rd party plugin to
	 * return prices converted to the active currency.
	 *
	 * @param double price The source price.
	 * @param string to_currency The target currency. If empty, the active currency
	 *               will be taken.
	 * @param string from_currency The source currency. If empty, WooCommerce base
	 *               currency will be taken.
	 *
	 * @return double The price converted from source to destination currency.
	 * @author Aelia <support@aelia.co>
	 * @link   http://aelia.co
	 */
	protected function get_price_in_currency( $price, $to_currency = NULL, $from_currency = NULL, $currencies = NULL, $type = NULL, $key = NULL, $attribute = NULL ) {

		if ( empty( $from_currency ) ) {
			$from_currency = $this->get_default_from_currency();
		}
		if ( empty( $to_currency ) ) {
			$to_currency = $this->get_default_to_currency();
		}
		if ( $from_currency == $to_currency ) {
			return $price;
		}
		if ( $type !== NULL && in_array( $type, array( '', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ) ) && is_array( $currencies ) && isset( $currencies[ $to_currency ] ) ) {// 'percentcurrenttotal',
			$v = $currencies[ $to_currency ];
			if ( $key !== NULL && isset( $v[ $key ] ) ) {
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

			return $v;
		}

		return apply_filters( 'wc_epo_cs_convert', apply_filters( 'wc_aelia_cs_convert', $price, $from_currency, $to_currency ), $from_currency, $to_currency );
	}

	/**
	 * Support for Product Price based on country
	 * (https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/)
	 *
	 * @since 1.0
	 */
	public function wc_epo_cs_convert( $amount, $from_currency, $to_currency, $include_markup = TRUE ) {
		if ( $this->is_aelia_currency_switcher ) {
			return $amount;
		}

		// No need to convert a zero amount, it will stay zero
		if ( $amount == 0 ) {
			return $amount;
		}

		// No need to spend time converting a currency to itself
		if ( $from_currency == $to_currency ) {
			return $amount;
		}

		// Retrieve exchange rates from the configuration
		$exchange_rate = FALSE;
		if ( class_exists( 'WC_Product_Price_Based_Country' ) && function_exists( 'WCPBC' ) ) {
			
			if ( function_exists( 'wcpbc_the_zone' )  && wcpbc_the_zone() ) {
			    $exchange_rate = wcpbc_the_zone()->get_exchange_rate();
			}

			if ( ! $exchange_rate ) {

				$customer = FALSE;
				if (isset(WCPBC()->customer)){
					$customer = WCPBC()->customer;
				}

				if ( ! $customer ) {
					$regions = get_option( 'wc_price_based_country_regions', array() );
					if (!is_array($regions)){
						$regions = array();
					}
					foreach ( $regions as $key => $value ) {
						if ( $value['currency'] == $to_currency ) {
							$exchange_rate = $value['exchange_rate'];
							break;
						}
					}
				} else if ($customer->exchange_rate){
					$exchange_rate = $customer->exchange_rate;
				}

			}

		}
		
		if ( ! $exchange_rate ) {
			return $amount;
		}

		return apply_filters( 'wc_epo_cs_converted_amount',
			round( $amount * $exchange_rate ),
			$amount,
			$from_currency,
			$to_currency );
	}

	/**
	 * Alter option prices in cart
	 *
	 * @since 1.0
	 */
	public function tm_epo_price_add_on_cart( $price = "", $price_type = "" ) {

		if ( ! $this->is_all_in_one_cc ) {
			$price = apply_filters( 'wc_epo_get_current_currency_price', $price, $price_type );
		}

		return $price;

	}

}
