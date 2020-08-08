<?php
/**
 * WCS_ATT_Product_Prices class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API for working with the prices of subscription-enabled product objects.
 *
 * @class    WCS_ATT_Product_Prices
 * @version  3.1.0
 */
class WCS_ATT_Product_Prices {

	/**
	 * Initialize.
	 */
	public static function init() {

		require_once( 'class-wcs-att-product-price-filters.php' );

		self::add_hooks();
	}

	/**
	 * Add price filters.
	 *
	 * @return void
	 */
	private static function add_hooks() {

		add_action( 'plugins_loaded', array( 'WCS_ATT_Product_Price_Filters', 'add' ), 99 );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns a string representing the details of the active subscription scheme.
	 *
	 * @param  WC_Product  $product  Product object.
	 * @param  array       $include  An associative array of flags to indicate how to calculate the price and what to include - @see 'WC_Subscriptions_Product::get_price_string'.
	 * @param  array       $args     Optional args to pass into 'WC_Subscriptions_Product::get_price_string'. Use 'scheme_key' to optionally define a scheme key to use.
	 * @return string
	 */
	public static function get_price_string( $product, $args = array() ) {

		$scheme_key = isset( $args[ 'scheme_key' ] ) ? $args[ 'scheme_key' ] : '';

		$active_scheme_key = WCS_ATT_Product_Schemes::get_subscription_scheme( $product );
		$scheme_key        = '' === $scheme_key ? $active_scheme_key : $scheme_key;

		// Attempt to switch scheme when requesting the price html of a scheme other than the active one.
		$scheme_switch_required = $scheme_key !== $active_scheme_key;
		$switched_scheme        = $scheme_switch_required ? WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $scheme_key ) : false;

		$price_string = WC_Subscriptions_Product::get_price_string( $product, $args );

		// Switch back to the initially active scheme, if switched.
		if ( $switched_scheme ) {
			WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $active_scheme_key );
		}

		return $price_string;
	}

	/**
	 * Returns the price html associated with the active subscription scheme.
	 * You may optionally pass a scheme key to get the price html string associated with it.
	 *
	 * @param  WC_Product  $product     Product object.
	 * @param  integer     $scheme_key  Scheme key or the currently active one, if undefined. Optional.
	 * @param  array       $args        Optional args to pass into 'WC_Subscriptions_Product::get_price_string'.
	 * @return string
	 */
	public static function get_price_html( $product, $scheme_key = '', $args = array() ) {

		$active_scheme_key = WCS_ATT_Product_Schemes::get_subscription_scheme( $product );
		$scheme_key        = '' === $scheme_key ? $active_scheme_key : $scheme_key;

		// Attempt to switch scheme when requesting the price html of a scheme other than the active one.
		$scheme_switch_required = $scheme_key !== $active_scheme_key;
		$switched_scheme        = $scheme_switch_required ? WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $scheme_key ) : false;

		// Scheme switch required but unsuccessful? Problem. Set price html to an empty string.
		if ( $scheme_switch_required && false === $switched_scheme ) {

			$price_html = '';

		} else {

			$price_html = '';
			$context    = isset( $args[ 'context' ] ) ? $args[ 'context' ] : 'catalog';

			/**
			 * 'wcsatt_price_html_args' filter.
			 *
			 * @since  3.0.0
			 *
			 * @param  WC_Product  $product
			 * @param  integer     $scheme_key
			 * @param  array       $args
			 */
			$args = apply_filters( 'wcsatt_price_html_args', $args, $product, $scheme_key );

			$subscribe_options_html    = ! empty( $args[ 'subscribe_options_html' ] ) ? $args[ 'subscribe_options_html' ] : _x( 'Subscribe', 'Subscribe call-to-action', 'woocommerce-all-products-for-subscriptions' );
			$subscribe_for_html        = ! empty( $args[ 'subscribe_for_html' ] ) ? $args[ 'subscribe_for_html' ] : _x( 'Subscribe for %s', 'Subscribe to plan', 'woocommerce-all-products-for-subscriptions' );
			$subscribe_from_html       = ! empty( $args[ 'subscribe_from_html' ] ) ? $args[ 'subscribe_from_html' ] : _x( 'Subscribe from %s', 'Subscribe to plans', 'woocommerce-all-products-for-subscriptions' );
			$subscribe_discounted_html = ! empty( $args[ 'subscribe_discounted_html' ] ) ? $args[ 'subscribe_discounted_html' ] : _x( 'Subscribe and save %1$s%2$s', 'Subscribe to plan(s) for discount', 'woocommerce-all-products-for-subscriptions' );

			$html_for_text             = _x( '<span class="for">for</span> ', 'subscription "for" price string', 'woocommerce-all-products-for-subscriptions' );
			$html_from_text            = _x( '<span class="from">from</span> ', 'subscription "from" price string', 'woocommerce-all-products-for-subscriptions' );
			$html_from_text_native     = wc_get_price_html_from_text();

			$include_sync_details      = isset( $args[ 'include_sync_details' ] ) ? $args[ 'include_sync_details' ] : false === in_array( $context, array( 'catalog', 'prompt' ) );

			// Scheme is set on the object? Just add the subscription details.
			if ( WCS_ATT_Product::is_subscription( $product ) ) {

				$details_html        = '';

				$schemes             = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
				$price_filter_exists = WCS_ATT_Product_Schemes::price_filter_exists( $schemes );
				$active_scheme       = WCS_ATT_Product_Schemes::get_subscription_scheme( $product, 'object' );

				// Include sync details in price string?
				$remove_synced_subscription_details = false === $include_sync_details && $active_scheme->is_synced();

				if ( $remove_synced_subscription_details ) {
					add_filter( 'pre_option_woocommerce_subscriptions_sync_payments', array( __CLASS__, 'remove_synced_subscription_details' ) );
				}

				$force_discount  = isset( $args[ 'force_discount' ] ) && $args[ 'force_discount' ];
				$append_discount = isset( $args[ 'append_discount' ] ) && $args[ 'append_discount' ];
				$append_price    = isset( $args[ 'append_price' ] ) && $args[ 'append_price' ];
				$hide_price      = isset( $args[ 'hide_price' ] ) && $args[ 'hide_price' ];

				if ( ! $active_scheme->get_discount() || 'dropdown' === $context ) {
					$force_discount  = false;
					$append_discount = false;
				}

				if ( 'catalog' === $context ) {
					$hide_price     = false;
					$force_discount = false;
				}

				$hide_string_price = false;

				if ( $hide_price || $force_discount || ( false === $price_filter_exists && 'catalog' !== $context ) ) {
					$hide_string_price = true;
					$append_price      = false;
				}

				// Generating price string without price amount?
				if ( $hide_string_price || $append_price ) {

					// When appending the price to the end, generate it here.
					if ( $append_price ) {
						$price_html = empty( $args[ 'price' ] ) ? self::get_price_html_unfiltered( $product ) : $args[ 'price' ];
					}

					$args[ 'price' ]           = '';
					$args[ 'tax_calculation' ] = false;

					if ( ! $active_scheme->is_synced() ) {
						$args[ 'subscription_price' ] = false;
					}

				} else {
					$args[ 'price' ] = empty( $args[ 'price' ] ) ? self::get_price_html_unfiltered( $product ) : $args[ 'price' ];
				}

				if ( $append_price ) {
					$details_html = WC_Subscriptions_Product::get_price_string( $product, $args );
				} else {
					$price_html = WC_Subscriptions_Product::get_price_string( $product, $args );
				}

				// Appending a discount string?
				if ( $force_discount || $append_discount ) {

					$discount      = $active_scheme->get_discount();
					$discount_html = '<span class="wcsatt-sub-discount">' . sprintf( _x( '%s&#37;', 'option discount', 'woocommerce-all-products-for-subscriptions' ), round( $discount, self::get_formatted_discount_precision() ) ) . '</span>';
					$price_html    = sprintf( _x( '%1$s &mdash; save %2$s', 'discounted option price html format', 'woocommerce-all-products-for-subscriptions' ), $price_html, $discount_html );
				}

				// Drop native "from" string.
				$has_variable_price = false;

				if ( false !== strpos( $price_html, $html_from_text_native ) ) {
					$price_html         = str_replace( $html_from_text_native, '', $price_html );
					$has_variable_price = true;
				}

				// Appending price at the end?
				if ( $append_price ) {

					// Add 'from' string.
					if ( $has_variable_price ) {
						$price_html = sprintf( _x( '%1$s from %2$s', 'subscription price string with price at the end', 'woocommerce-all-products-for-subscriptions' ), $details_html, $price_html );
					} else {
						$price_html = sprintf( _x( '%1$s for %2$s', 'subscription price string with price at the end', 'woocommerce-all-products-for-subscriptions' ), $details_html, $price_html );
					}

				} else {

					// Add 'from' string.
					if ( $has_variable_price ) {
						$price_html = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-all-products-for-subscriptions' ), 'catalog' === $context ? $html_from_text_native : $html_from_text, $price_html );
					}
				}

				if ( $remove_synced_subscription_details ) {
					remove_filter( 'pre_option_woocommerce_subscriptions_sync_payments', array( __CLASS__, 'remove_synced_subscription_details' ) );
				}

			// Subscription state is undefined? Construct a special price string.
			} elseif ( is_null( $scheme_key ) ) {

				$schemes         = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
				$base_scheme     = WCS_ATT_Product_Schemes::get_base_subscription_scheme( $product );
				$base_scheme_key = $base_scheme->get_key();

				// Include sync details in price string?
				$remove_synced_subscription_details = false === $include_sync_details && $base_scheme->is_synced();

				if ( $remove_synced_subscription_details ) {
					add_filter( 'pre_option_woocommerce_subscriptions_sync_payments', array( __CLASS__, 'remove_synced_subscription_details' ) );
				}

				if ( $product->is_type( 'variable' ) && $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ) ) {
					$has_variable_price = true;
				} elseif (  $product->is_type( 'bundle' ) && $product->get_bundle_price( 'min' ) !== $product->get_bundle_price( 'max' ) ) {
					$has_variable_price = true;
				} elseif ( $product->is_type( 'composite' ) && $product->get_composite_price( 'min' ) !== $product->get_composite_price( 'max' ) ) {
					$has_variable_price = true;
				} else {
					$has_variable_price = false;
				}

				if ( WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product ) ) {

					// Temporarily apply base scheme on product object.
					$switched_scheme = WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $base_scheme_key );

					// Get base scheme price string.
					$price_html = WC_Subscriptions_Product::get_price_string( $product, self::get_base_subscription_scheme_price_html_args( $args, $product ) );

					// Drop native "from" string.
					if ( false !== strpos( $price_html, $html_from_text_native ) ) {
						$price_html         = str_replace( $html_from_text_native, '', $price_html );
						$has_variable_price = true;
					}

					// Add "from" string.
					if ( $has_variable_price || sizeof( $schemes ) > 1 ) {

						if ( 'prompt' === $context ) {

							$price_html = sprintf( $subscribe_from_html, '<span class="price subscription-price">' . $price_html . '</span>' );

						} else {

							$add_html_from_text = true;

							if ( $product->is_type( 'variable' ) && sizeof( $schemes ) === 1 ) {
								$add_html_from_text = false;
							}

							if ( $add_html_from_text ) {
								$price_html = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-all-products-for-subscriptions' ), 'catalog' === $context ? $html_from_text_native : $html_from_text, $price_html );
							}
						}

					// Merge into "subscribe" string when applicable.
					} elseif ( 'prompt' === $context ) {

						$price_html = sprintf( $subscribe_for_html, '<span class="price subscription-price">' . $price_html . '</span>' );
					}

				} else {

					// Get bare price string before switch.
					if ( 'catalog' === $context ) {
						$price_html = empty( $args[ 'price' ] ) ? self::get_price_html_unfiltered( $product ) : $args[ 'price' ];
					}

					$discount                   = '';
					$suffix_price_html          = '';
					$allow_discount_html_format = true;
					$has_variable_discount      = false;
					$price_filter_exists        = WCS_ATT_Product_Schemes::price_filter_exists( $schemes );
					$switched_scheme            = WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $base_scheme_key );

					if ( ! $price_filter_exists ) {
						$allow_discount_html_format = false;
					} elseif ( in_array( $context, array( 'catalog', 'prompt' ) ) ) {
						if ( sizeof( $schemes ) === 1 ) {
							$allow_discount_html_format = false;
						} elseif ( isset( $args[ 'allow_discount' ] ) && false === $args[ 'allow_discount' ] ) {
							$allow_discount_html_format = false;
						}
					}

					// Show discount format if all schemes are of the 'inherit' pricing mode type.
					if ( $allow_discount_html_format ) {

						foreach ( $schemes as $scheme ) {
							if ( $scheme->has_price_filter() ) {

								if ( 'inherit' !== $scheme->get_pricing_mode() ) {

									$allow_discount_html_format = false;
									break;

								} elseif ( $discount !== $scheme->get_discount() ) {

									if ( '' === $discount ) {
										$discount = $scheme->get_discount();
									} else {
										$has_variable_discount = true;
									}
								}

							} else {
								$has_variable_discount = true;
							}
						}
					}

					$allow_discount_html_format = apply_filters( 'wcsatt_price_html_discount_format', $allow_discount_html_format, $product, $args );

					// Using discount format?
					if ( $allow_discount_html_format ) {

						// Merge into "subscribe" string when applicable.

						if ( 'prompt' === $context ) {

							$discount_html = ' <span class="wcsatt-sub-discount">' . sprintf( _x( '%s&#37;', 'subscribe and save discount', 'woocommerce-all-products-for-subscriptions' ), round( $base_scheme->get_discount(), self::get_formatted_discount_precision() ) ) . '</span>';
							$price_html    = sprintf( $subscribe_discounted_html, $has_variable_discount ? __( 'up to', 'woocommerce-all-products-for-subscriptions' ) : '', $discount_html );

						} else {

							$discount_html     = '</small> <span class="wcsatt-sub-discount">' . sprintf( _x( '%s&#37;', 'subscribe and save discount', 'woocommerce-all-products-for-subscriptions' ), round( $base_scheme->get_discount(), self::get_formatted_discount_precision() ) ) . '</span><small>';
							$suffix_price_html = sprintf( __( 'subscribe and save %1$s%2$s', 'woocommerce-all-products-for-subscriptions' ), $has_variable_discount ? __( 'up to', 'woocommerce-all-products-for-subscriptions' ) : '', $discount_html );
							$suffix            = '<small class="wcsatt-sub-options">' . sprintf( _x( ' <span class="wcsatt-dash">&mdash;</span> or %s', 'subscribe and save suffix format', 'woocommerce-all-products-for-subscriptions' ), $suffix_price_html ) . '</small>';
						}

					} else {

						$base_scheme_price_html = WC_Subscriptions_Product::get_price_string( $product, self::get_base_subscription_scheme_price_html_args( $args, $product ) );

						if ( 'prompt' === $context ) {

							// Merge into "subscribe" string when applicable.

							$base_scheme_price_html = str_replace( $html_from_text_native, '', $base_scheme_price_html );

							if ( $price_filter_exists ) {

								if ( sizeof( $schemes ) > 1 ) {
									$price_html = sprintf( $subscribe_from_html, '<span class="price subscription-price">' . $base_scheme_price_html . '</span>' );
								} else {
									$price_html = sprintf( $subscribe_for_html, '<span class="price subscription-price">' . $base_scheme_price_html . '</span>' );
								}

							} else {

								if ( sizeof( $schemes ) > 1 ) {
									$price_html = sprintf( $subscribe_options_html, '<span class="no-price subscription-price">' . $base_scheme_price_html . '</span>' );
								} else {
									$price_html = sprintf( $subscribe_for_html, '<span class="price subscription-price">' . $base_scheme_price_html . '</span>' );
								}
							}

						} else {

							if ( sizeof( $schemes ) > 1 ) {
								$suffix_price_html = sprintf( _x( '%1$s%2$s', 'Price range: starting at', 'woocommerce-all-products-for-subscriptions' ), _x( '<span class="from">from</span> ', 'subscriptions "starting at" price string', 'woocommerce-all-products-for-subscriptions' ), str_replace( $html_from_text_native, '', $base_scheme_price_html ) );
							} elseif ( $has_variable_price ) {
								$suffix_price_html = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-all-products-for-subscriptions' ), _x( '<span class="from">from</span> ', 'subscription "from" price string', 'woocommerce-all-products-for-subscriptions' ), str_replace( $html_from_text_native, '', $base_scheme_price_html ) );
							} else {
								$suffix_price_html = $base_scheme_price_html;
							}

							if ( $price_filter_exists ) {
								$suffix = '<small class="wcsatt-sub-options">' . sprintf( _n( ' <span class="wcsatt-dash">&mdash;</span> or %s', ' <span class="wcsatt-dash">&mdash;</span> available on subscription %s', sizeof( $schemes ), 'woocommerce-all-products-for-subscriptions' ), $suffix_price_html ) . '</small>';
							} else {
								$suffix = '<small class="wcsatt-sub-options">' . sprintf( _n( ' <span class="wcsatt-dash">&mdash;</span> available on subscription', ' <span class="wcsatt-dash">&mdash;</span> available on subscription', sizeof( $schemes ), 'woocommerce-all-products-for-subscriptions' ), $suffix_price_html ) . '</small>';
							}
						}
					}

					if ( 'prompt' !== $context ) {

						/**
						 * 'wcsatt_price_html_suffix' filter
						 *
						 * @since  3.0.0
						 *
						 * @param  string      $suffix
						 * @param  WC_Product  $product
						 * @param  array       $args
						 */
						$suffix     = apply_filters( 'wcsatt_price_html_suffix', $suffix, $product, $args );
						$price_html = sprintf( _x( '%1$s%2$s', 'product sub options price html suffix', 'woocommerce-all-products-for-subscriptions' ), $price_html, $suffix );
					}
				}

				if ( $remove_synced_subscription_details ) {
					remove_filter( 'pre_option_woocommerce_subscriptions_sync_payments', array( __CLASS__, 'remove_synced_subscription_details' ) );
				}

			} elseif ( false === $scheme_key ) {
				$price_html = empty( $args[ 'price' ] ) ? self::get_price_html_unfiltered( $product ) : $args[ 'price' ];
			}
		}

		// Switch back to the initially active scheme, if switched.
		if ( $switched_scheme ) {
			WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $active_scheme_key );
		}

		return $price_html;
	}

	/**
	 * Base subscription scheme price html args.
	 *
	 * @since  3.0.0
	 *
	 * @param  array       $args
	 * @param  WC_Product  $product
	 * @return array
	 */
	protected static function get_base_subscription_scheme_price_html_args( $args, $product ) {

		// Base price already defined?
		$args[ 'price' ] = empty( $args[ 'base_price' ] ) ? self::get_price_html_unfiltered( $product ) : $args[ 'base_price' ];

		return apply_filters( 'wcsatt_undefined_scheme_price_html_args', $args, $product );
	}

	/**
	 * Unfiltered alias of 'WC_Product::get_price_html'.
	 *
	 * @param  WC_Product  $product  Product object.
	 * @return string
	 */
	public static function get_price_html_unfiltered( $product ) {

		WCS_ATT_Product_Price_Filters::remove( 'price_html' );
		$price_html = $product->get_price_html();
		WCS_ATT_Product_Price_Filters::add( 'price_html' );

		return $price_html;
	}

	/**
	 * Returns the recurring vanilla/regular/sale price.
	 *
	 * @param  WC_Product  $product     Product object.
	 * @param  string      $scheme_key  Optional key to get the price of a specific scheme.
	 * @param  string      $context     Function call context.
	 * @param  string      $price_type  Price to get. Values: '', 'regular', or 'sale'.
	 * @return mixed                    The price charged charged per subscription period.
	 */
	protected static function get_product_price( $product, $scheme_key = '', $context = 'view', $price_type = '' ) {

		$price_type = $price_type && in_array( $price_type, array( 'regular', 'sale' ) ) ? $price_type : '';
		$price_prop = $price_type ? $price_type . '_price' : 'price';
		$price_fn   = 'get_' . $price_prop;

		// In 'view' context, switch the active scheme if needed - and call 'WC_Product::get_{price_prop}'.
		if ( 'view' === $context ) {

			$active_scheme_key = WCS_ATT_Product_Schemes::get_subscription_scheme( $product );
			$scheme_key        = '' === $scheme_key ? $active_scheme_key : $scheme_key;

			// Attempt to switch scheme when requesting the price html of a scheme other than the active one.
			$scheme_switch_required = $scheme_key !== $active_scheme_key;
			$switched_scheme        = $scheme_switch_required ? WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $scheme_key ) : false;

			$price = $product->$price_fn();

			// Switch back to the initially active scheme, if switched.
			if ( $switched_scheme ) {
				WCS_ATT_Product_Schemes::set_subscription_scheme( $product, $active_scheme_key );
			}

		// In 'edit' context, just grab the raw price from the product prop, applying overrides if present.
		} else {

			$subscription_scheme = WCS_ATT_Product_Schemes::get_subscription_scheme( $product, 'object', $scheme_key );
			$price               = $product->$price_fn( 'edit' );

			if ( ! empty( $subscription_scheme ) ) {

				if ( $subscription_scheme->has_price_filter() && 'override' === $subscription_scheme->get_pricing_mode() && WCS_ATT_Product_Price_Filters::filter_plan_prices( $product ) ) {

					$prices_array = array(
						'price'         => 'price'         === $price_prop ? $price : $product->get_price( 'edit' ),
						'sale_price'    => 'sale_price'    === $price_prop ? $price : $product->get_sale_price( 'edit' ),
						'regular_price' => 'regular_price' === $price_prop ? $price : $product->get_regular_price( 'edit' ),
						'offset_price'  => WCS_ATT_Product::get_runtime_meta( $product, 'price_offset' ) // See 'WCS_ATT_Integration_PAO::backup_addon_price'.
					);

					$overridden_prices = $subscription_scheme->get_prices( $prices_array );
					$price             = $overridden_prices[ $price_prop ];
				}
			}

			if ( '' === $price && 'sale_price' !== $price_prop && $product->is_type( array( 'bundle', 'composite' ) ) && $product->contains( 'priced_individually' ) ) {
				$price = (double) $price;
			}
		}

		return $price;
	}

	/**
	 * Returns the recurring price.
	 *
	 * @param  WC_Product  $product     Product object.
	 * @param  string      $scheme_key  Optional key to get the price of a specific scheme.
	 * @param  string      $context     Function call context.
	 * @return mixed                    The price charged per subscription period.
	 */
	public static function get_price( $product, $scheme_key = '', $context = 'view' ) {
		return self::get_product_price( $product, $scheme_key, $context );
	}

	/**
	 * Returns the recurring regular price.
	 *
	 * @param  WC_Product  $product     Product object.
	 * @param  string      $scheme_key  Optional key to get the regular price of a specific scheme.
	 * @param  string      $context     Function call context.
	 * @return mixed                    The regular price charged per subscription period.
	 */
	public static function get_regular_price( $product, $scheme_key = '', $context = 'view' ) {
		return self::get_product_price( $product, $scheme_key, $context, 'regular' );
	}

	/**
	 * Returns the recurring sale price.
	 *
	 * @param  WC_Product  $product     Product object.
	 * @param  string      $scheme_key  Optional key to get the price of a specific scheme.
	 * @param  string      $context     Function call context.
	 * @return mixed                    The sale price charged per subscription period.
	 */
	public static function get_sale_price( $product, $scheme_key = '', $context = 'view' ) {
		return self::get_product_price( $product, $scheme_key, $context, 'sale' );
	}

	/**
	 * Generated formatted discount string. Used in dropdowns.
	 *
	 * @since  3.0.0
	 *
	 * @return string|false
	 */
	public static function get_formatted_discount( $product, $scheme ) {

		$formatted_discount = '';

		if ( ! $scheme->has_price_filter() ) {
			return $formatted_discount;
		}

		if ( $discount = $scheme->get_discount() ) {

			$formatted_discount = sprintf( _x( '%s%%', 'dropdown option discount', 'woocommerce-all-products-for-subscriptions' ), round( $discount, self::get_formatted_discount_precision() ) );

		} else {

			$price         = self::get_price( $product, $scheme->get_key() );
			$regular_price = self::get_regular_price( $product, $scheme->get_key() );

			if ( $regular_price > $price ) {
				$formatted_discount = sprintf( _x( '%s%%', 'dropdown option discount', 'woocommerce-all-products-for-subscriptions' ), round( 100 * ( $regular_price - $price ) / $regular_price, self::get_formatted_discount_precision() ) );
			}
		}

		return $formatted_discount;
	}

	/**
	 * Precision for discounts displayed in price strings.
	 *
	 * @since  3.0.0
	 *
	 * @return int
	 */
	public static function get_formatted_discount_precision() {
		return apply_filters( 'wcsatt_formatted_discount_precision', 1 );
	}

	/**
	 * Format prices without html content. Used in dropdowns.
	 *
	 * @since  3.0.0
	 *
	 * @param  mixed  $price
	 * @param  array  $args
	 * @return string
	 */
	public static function get_formatted_price( $price ) {

		$num_decimals    = wc_get_price_decimals();
		$decimal_sep     = wc_get_price_decimal_separator();
		$thousands_sep   = wc_get_price_thousand_separator();
		$currency_symbol = get_woocommerce_currency_symbol();
		$price_format    = get_woocommerce_price_format();

		$price = apply_filters( 'raw_woocommerce_price', floatval( $price ) );
		$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $num_decimals, $decimal_sep, $thousands_sep ), $price, $num_decimals, $decimal_sep, $thousands_sep );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		return sprintf( $price_format, $currency_symbol, $price );
	}

	/**
	 * Callback used to remove subscription sync details from price strings.
	 *
	 * @since  3.0.0
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function remove_synced_subscription_details( $value ) {
		return 'no';
	}
}

WCS_ATT_Product_Prices::init();
