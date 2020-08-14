<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher;

if ( ! defined( 'YITH_WPV_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Vendor_Shipping_Frontend
 * @package    Yithemes
 * @since      Version 1.9.17
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Vendor_Shipping_Frontend' ) ) {

	/**
	 * Class YITH_Vendors_Shipping_Frontend
	 *
	 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
	 */
	class YITH_Vendor_Shipping_Frontend {

		private $vendor_cart_elements = array();

		/**
		 * Constructor
		 *
		 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
		 */
		public function __construct() {

			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woocommerce_cart_shipping_packages' ) );

			add_filter( 'woocommerce_shipping_packages', array( $this, 'woocommerce_shipping_packages' ) );

			add_filter( 'woocommerce_shipping_package_name', array(
				$this,
				'woocommerce_shipping_package_name'
			), 10, 3 );

			add_filter( 'woocommerce_package_rates', array( $this, 'woocommerce_package_rates' ), 10, 2 );

			//Register shipping fee commissions
			add_action( 'yith_wcmv_checkout_order_processed', 'YITH_Vendor_Shipping::register_commissions', 15, 1 );
		}

		/**
		 * @param $packages
		 *
		 * @return array
		 */
		public function woocommerce_shipping_packages( $packages ) {
			foreach ( $packages as $key => $package ) {
				if ( empty( $package['contents'] ) ) {
					unset( $packages[ $key ] );
				}
			}

			return $packages;
		}

		/**
		 * @param $packages
		 *
		 * @return array
		 */
		public function woocommerce_cart_shipping_packages( $packages, $wc_cart = null ) {

			$wc_cart = is_null( $wc_cart ) ? WC()->cart : $wc_cart;

			$this->vendor_cart_elements = array();

			$vendors = $this->get_vendors_in_cart( $wc_cart->get_cart() );

			if ( count( $vendors ) > 0 ) {
				$destination_country   = strtoupper( WC()->customer->get_shipping_country() );
				$destination_continent = strtoupper( wc_clean( WC()->countries->get_continent_code_for_country( $destination_country ) ) );
				$destination_state     = strtoupper( wc_clean( WC()->customer->get_shipping_state() ) );
				$destination_postcode  = strtoupper( wc_clean( WC()->customer->get_shipping_postcode() ) );

				foreach ( $vendors as $vendor ) {
					if ( YITH_Vendor_Shipping::is_single_vendor_shipping_enabled( $vendor ) ) {
						$zone_key = $this->get_matched_zone( $vendor, $destination_country, $destination_continent, $destination_state, $destination_postcode );

						if ( $zone_key != '' ) {
							$packages[] = $package = $this->get_package( $wc_cart, $vendor, $zone_key );
						}
					}
				}

				// Remove vendor products from WooCommerce shipping packages
				foreach ( $packages as &$package ) {
					if ( ! isset( $package['yith-vendor'] ) ) {

						if ( empty( $package['contents_cost'] ) ) {
							$package['contents_cost'] = 0;
						}

						if ( empty( $package['contents_taxes_cost'] ) ) {
							$package['contents_taxes_cost'] = 0;
						}

						if ( count( $this->vendor_cart_elements ) > 0 ) {
							// remove elements
							foreach ( $this->vendor_cart_elements as $product_vendor_cart_key ) {
								if ( apply_filters( 'yith_wcmv_vendor_cart_elements_package', true, $product_vendor_cart_key, $package ) ) {
									unset( $package['contents'][ $product_vendor_cart_key ] );
								}
							}

							// recalculate contents_cost
							foreach ( $package['contents'] as $item ) {
								if ( $item['data']->needs_shipping() ) {
									if ( isset( $item['line_total'] ) ) {
										$package['contents_cost'] += $item['line_total'];
									}

									if ( isset( $item['line_tax'] ) ) {
										$package['contents_taxes_cost'] += $item['line_tax'];
									}
								}
							}

						}
					}
				}
			}

			return $packages;

		}

		/**
		 * @param $wc_cart
		 * @param $vendor
		 * @param $zone_key
		 *
		 * @return array
		 */
		private function get_package( $wc_cart, $vendor, $zone_key ) {

			$package                                  = array();
			$cart_elements                            = $wc_cart->get_cart();
			$package['contents']                      = $this->get_vendors_cart_contens( $vendor, $cart_elements );        // Items in the package
			$package['contents_cost']                 = 0;                    // Cost of items in the package, set below
			$package['contents_taxes_cost']           = 0;                    // Cost of items taxes in the package, set below
			$package['applied_coupons']               = array();
			$package['user']['ID']                    = get_current_user_id();
			$package['destination']['country']        = WC()->customer->get_shipping_country();
			$package['destination']['state']          = WC()->customer->get_shipping_state();
			$package['destination']['postcode']       = WC()->customer->get_shipping_postcode();
			$package['destination']['city']           = WC()->customer->get_shipping_city();
			$package['destination']['address']        = WC()->customer->get_shipping_address();
			$package['destination']['address_2']      = WC()->customer->get_shipping_address_2();
			$package['yith-vendor']                   = $vendor;
			$package['yith-vendor-shipping-zone-key'] = $zone_key;

			foreach ( $package['contents'] as $item ) {
				/** @var $item_data WC_Product */
				$item_data = $item['data'];

				if ( $item_data->needs_shipping() ) {
					if ( isset( $item['line_total'] ) ) {
						$package['contents_cost'] += $item['line_total'];
					}

					if ( isset( $item['line_tax'] ) ) {
						$package['contents_taxes_cost'] += $item['line_tax'];
					}
				}
			}

			return $package;

		}

		/**
		 * @param $cart_contents
		 *
		 * @return array
		 */
		private function get_vendors_in_cart( $cart_contents ) {

			$vendors = array();

			foreach ( $cart_contents as $cart_item ) {

				if ( isset( $cart_item['data'] ) ) {

					$vendor = yith_get_vendor( yit_get_base_product_id( $cart_item['data'] ), 'product' );

					if ( $vendor->is_valid() && YITH_Vendor_Shipping::is_single_vendor_shipping_enabled( $vendor ) && ( ! in_array( $vendor, $vendors ) ) ) {

						$vendors[] = $vendor;

					}
				}

			}

			return $vendors;

		}

		/**
		 * @param $vendor
		 * @param $cart_contents
		 *
		 * @return array
		 */
		private function get_vendors_cart_contens( $vendor, $cart_contents ) {

			$cart_elements = array();

			foreach ( $cart_contents as $key => $cart_item ) {

				if ( isset( $cart_item['data'] ) ) {

					$product = $cart_item['data'];

					if ( ! $product->is_virtual() && ! $product->is_downloadable() ) {

						$product_id = wp_get_post_parent_id( $product->get_id() ) ? wp_get_post_parent_id( $product->get_id() ) : $product->get_id();

						$current_vendor = yith_get_vendor( $product_id, 'product' );

						if ( $current_vendor->id == $vendor->id && YITH_Vendor_Shipping::is_single_vendor_shipping_enabled( $vendor ) ) {

							$cart_elements[ $key ] = $cart_item;

							$this->vendor_cart_elements[] = $key;
						}

					}

				}

			}

			return $cart_elements;

		}

		/**
		 * @param $vendor
		 * @param $destination_country
		 * @param $destination_continent
		 * @param $destination_state
		 * @param $destination_postcode
		 *
		 * @return bool|int|string
		 */
		private function get_matched_zone( $vendor, $destination_country, $destination_continent, $destination_state, $destination_postcode ) {
			$search_destination_continent = 'continent:' . $destination_continent;
			$search_destination_country   = 'country:' . $destination_country;
			$search_destination_state     = 'state:' . $destination_country . ':' . $destination_state;
			$zone_data                    = maybe_unserialize( $vendor->zone_data );
			$matched_zone_keys            = array();
			$matched_zone_key             = '';

			if ( is_array( $zone_data ) ) {
				foreach ( $zone_data as $key => $zone ) {
					if ( isset( $zone['zone_regions'] ) ) {
						$zone_regions = $zone['zone_regions'];
						if( false !== array_search( 'continent:all', $zone['zone_regions'] ) ){
							$matched_zone_keys[] = $key;
						}

						else {
							foreach ( $zone_regions as $region ) {
								$is_macthed = ( $region == $search_destination_continent ) || ( $region == $search_destination_country ) || ( $region == $search_destination_state );

								if ( $is_macthed ) {
									$matched_zone_keys[] = $key;
								}
							}

						}
					}
				}
			}

			if ( ! empty( $matched_zone_keys ) ) {
				$postcodes_checked = false;

				/**
				 * Check for postal code
				 */
				foreach ( $matched_zone_keys as $zone_key ) {
					if ( ! empty( $vendor->zone_data[ $zone_key ]['zone_post_code'] ) ) {
						$postcodes_checked = true;
						$postcodes         = explode( PHP_EOL, $vendor->zone_data[ $zone_key ]['zone_post_code'] );
						foreach ( $postcodes as $postcode ) {
							$postcode    = trim( $postcode );
							$is_postcode = ! empty( $postcode ) ? WC_Validation::is_postcode( $postcode, $destination_country ) : false;

							if ( $is_postcode ) {
								if ( $postcode == $destination_postcode ) {
									$matched_zone_key = $zone_key;
								}
							} else {
								//Check for range or wildcard postalcode
								$is_range     = strrpos( $postcode, '...' );
								$is_wildcards = strrpos( $postcode, '*' );

								if ( $is_range ) {
									$postcode_range = explode( '...', $postcode );
									$min            = min( $postcode_range );
									$max            = max( $postcode_range );

									if ( $destination_postcode >= $min && $destination_postcode <= $max ) {
										$matched_zone_key = $zone_key;
									}
								} elseif ( $is_wildcards ) {
									$postcode          = str_replace( '*', '', $postcode );
									$regex             = "/^{$postcode}/";
									$is_valid_postcode = preg_match( $regex, $destination_postcode );

									if ( $is_valid_postcode ) {
										$matched_zone_key = $zone_key;
									}
								}

							}

							if ( ! empty( $matched_zone_key ) ) {
								break;
							}
						}
					}

					if ( ! empty( $matched_zone_key ) ) {
						break;
					}

					else {
						$postcodes_checked = false;
					}
				}

				if ( ! $postcodes_checked ) {
					$matched_zone_key = array_shift( $matched_zone_keys );
				}
			}

			return $matched_zone_key;
		}

		/**
		 * @param $title
		 * @param $i
		 * @param $package
		 *
		 * @return mixed
		 */
		public function woocommerce_shipping_package_name( $title, $i, $package ) {

			if ( isset( $package['yith-vendor-shipping-zone-key'] ) ) {

				$title = apply_filters( 'yith_vendor_package_name', $package['yith-vendor']->name, $package['yith-vendor'], $i );

			}

			return $title;
		}

		/**
		 * @param $rates
		 * @param $package
		 *
		 * @return array
		 */
		public function woocommerce_package_rates( $rates, $package ) {
			if ( isset( $package['yith-vendor-shipping-zone-key'] ) ) {

				$key = $package['yith-vendor-shipping-zone-key'];

				if ( $key && isset( $package['yith-vendor'] ) ) {

					$vendor = $package['yith-vendor'];

					if ( YITH_Vendor_Shipping::is_single_vendor_shipping_enabled( $vendor ) ) {

						$is_counpon_free_shipping = $this->is_coupon_free_shipping( $vendor );

						$rates = array();

						$zone_data = maybe_unserialize( $vendor->zone_data );

						if ( is_array( $zone_data ) && isset( $zone_data[ $key ] ) ) {

							$zone = $zone_data[ $key ];

							if ( isset( $zone['zone_shipping_methods'] ) ) {

								$zone_shipping_methods = $zone['zone_shipping_methods'];

								foreach ( $zone_shipping_methods as $key => $shipping_method ) {
									$this->addShippingRate( $package, $vendor, $key, $is_counpon_free_shipping, $shipping_method, $rates );
								}
							}
						}
					}
				}
			}

			return $rates;
		}

		/**
		 * @param $vendor
		 * @param $package
		 * @param $is_counpon_free_shipping
		 * @param $shipping_method
		 * @param $rates
		 */
		private function addShippingRate( $package, $vendor, $key, $is_counpon_free_shipping, $shipping_method, &$rates ) {

			if ( ! class_exists( 'WC_Eval_Math' ) ) {
				include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';
			}

			$is_free_shipping     = $shipping_method['type_id'] == 'free_shipping';
			$no_extra_cost_method = apply_filters( 'yith_wcmv_shipping_method_without_extra_cost', array(
				'free_shipping',
			) );
			$no_extra_cost        = in_array( $shipping_method['type_id'], $no_extra_cost_method );

			$locale   = localeconv();
			$decimals = array(
				wc_get_price_decimal_separator(),
				$locale['decimal_point'],
				$locale['mon_decimal_point'],
				','
			);

			// Remove whitespace from string.
			$shipping_method['method_cost'] = preg_replace( '/\s+/', '', $shipping_method['method_cost'] );

			// Remove locale from string.
			$shipping_method['method_cost'] = str_replace( $decimals, '.', $shipping_method['method_cost'] );

			// Trim invalid start/end characters.
			$shipping_method['method_cost'] = rtrim( ltrim( $shipping_method['method_cost'], "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

			$shipping_method['method_cost'] = ! empty( $shipping_method['method_cost'] ) ? WC_Eval_Math::evaluate( $shipping_method['method_cost'] ) : $shipping_method['method_cost'];

			$total_cost = wc_format_decimal( $shipping_method['method_cost'], wc_get_price_decimals() );

			if ( ! $no_extra_cost ) {
				$total_cost += $this->get_extra_cost( $vendor, $package, $shipping_method );
			}

			$tax_rate = ( $shipping_method['method_tax_status'] == 'none' ) ? false : '';

			$enable_current_rate = $this->is_enable_rate( $package, $is_free_shipping, $shipping_method, $is_counpon_free_shipping );

			$rate = null;

			/**
			 * Aelia Currency Switcher Support
			 */
			if ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
				$aelia_obj       = $GLOBALS[ WC_Aelia_CurrencySwitcher::$plugin_slug ];
				$base_currency   = is_callable( array(
					$aelia_obj,
					'base_currency'
				) ) ? $aelia_obj->base_currency() : get_woocommerce_currency();
				$current_country = is_callable( array(
					$aelia_obj,
					'get_selected_currency'
				) ) ? $aelia_obj->get_selected_currency() : get_woocommerce_currency();
				$total_cost      = apply_filters( 'wc_aelia_cs_convert', $total_cost, $base_currency, $current_country );
			}

			if ( $enable_current_rate ) {
				// Create rate object
				$rate                         = new WC_Shipping_Rate( $shipping_method['type_id'] . '_' . $key, $shipping_method['method_title'], $total_cost, '', $shipping_method['type_id'] );
				$shipping_object_id           = $shipping_method['type_id'] . '_' . $key;
				$rates[ $shipping_object_id ] = $rate;

				if ( ! empty( $rate ) && ! is_array( $tax_rate ) && $tax_rate !== false && $total_cost > 0 ) {
					//$taxes = $this->get_taxes_per_item( $total_cost );
					$rates[ $shipping_object_id ]->taxes = WC_Tax::calc_shipping_tax( $total_cost, WC_Tax::get_shipping_tax_rates() );
				}
			}
		}

		/**
		 * @param $vendor
		 * @param $package
		 *
		 * @return string
		 */
		private function get_extra_cost( $vendor, $package, $shipping_method ) {
			$products                          = $package['contents'];
			$shipping_product_additional_price = (float) $vendor->shipping_product_additional_price * ( count( $products ) - 1 );

			$shipping_product_qty_price_total_amount = $shipping_class_cost = 0;
			$shipping_class_costs                    = array();

			foreach ( $products as $product ) {
				$check                                   = false;
				$shipping_product_qty_price_total_amount += ( ( $product['quantity'] - 1 ) * (float) $vendor->shipping_product_qty_price );

				//Shipping Class Management
				if ( ! empty( $product ) && $product['data'] instanceof WC_Product ) {
					/** @var WC_Product $product ['data'] */
					$product_shipping_class_id = $product['data']->get_shipping_class_id( 'edit' );

					if ( empty( $product_shipping_class_id ) ) {
						$product_shipping_class_id = 'no_class_cost';
						$check                     = true;
					} else {
						$check = term_exists( $product_shipping_class_id, 'product_shipping_class' );

						/**
						 * @look at wp-includes/taxonomy.php:1297
						 *
						 * Returns null if the term does not exist. Returns the term ID
						 * if no taxonomy is specified and the term ID exists. Returns
						 * an array of the term ID and the term taxonomy ID the taxonomy
						 * is specified and the pairing exists.
						 */
						if ( ! is_null( $check ) ) {
							$product_shipping_class_id = 'class_cost_' . $product_shipping_class_id;
							$check                     = true;
						}
					}

					if ( true === $check ) {

						if ( isset( $shipping_method[ $product_shipping_class_id ] ) ) {
							$shipping_class_costs[ $product_shipping_class_id ] = $shipping_method[ $product_shipping_class_id ];
						}
					}
				}
			}

			$shipping_class_type = ! empty( $shipping_method['type'] ) ? $shipping_method['type'] : 'class';

			foreach ( $shipping_class_costs as $sc_id => $sc_cost ) {
				if ( 'class' == $shipping_class_type ) {
					$shipping_class_cost += (float) $sc_cost;
				} elseif ( 'order' == $shipping_class_type ) {
					$shipping_class_cost = (float) $sc_cost > $shipping_class_cost ? (float) $sc_cost : $shipping_class_cost;
				}
			}

			$locale = localeconv();

			$decimals = array(
				wc_get_price_decimal_separator(),
				$locale['decimal_point'],
				$locale['mon_decimal_point'],
				','
			);

			$total_extra_cost = (float) $vendor->shipping_default_price + $shipping_product_additional_price + $shipping_product_qty_price_total_amount + $shipping_class_cost;;

			// Remove whitespace from string.
			$total_extra_cost = preg_replace( '/\s+/', '', $total_extra_cost );

			// Remove locale from string.
			$total_extra_cost = str_replace( $decimals, '.', $total_extra_cost );

			// Trim invalid start/end characters.
			$total_extra_cost = rtrim( ltrim( $total_extra_cost, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

			// Do the math.
			if ( empty( $total_extra_cost ) ) {
				$total_extra_cost = 0;
			} else {
				$total_extra_cost = class_exists( 'WC_Eval_Math' ) ? WC_Eval_Math::evaluate( $total_extra_cost ) : $total_extra_cost;
			}

			return wc_format_decimal( $total_extra_cost, wc_get_price_decimals() );
		}


		private function is_enable_rate( $package, $is_free_shipping, $shipping_method, $is_counpon_free_shipping ) {

			$enable_current_rate = true;

			// check free shipping enabled
			if ( $is_free_shipping ) {
				/**
				 * Support for WooCommerce Currency Switcher
				 */
				global $WOOCS;
				if ( ! empty( $WOOCS ) ) {
					if ( $WOOCS->current_currency != $WOOCS->default_currency && get_option( 'woocs_is_multiple_allowed', 0 ) ) {
						$currencies = $WOOCS->get_currencies();
						//convert amount into basic currency amount
						$shipping_method['min_amount'] = $WOOCS->woocs_exchange_value( $shipping_method['min_amount'] );
					}
				}

				$min_amount         = wc_format_decimal( $shipping_method['min_amount'], wc_get_price_decimals() );
				$has_met_min_amount = 0;

				$total = round( $package['contents_cost'] + $package['contents_taxes_cost'], wc_get_price_decimals() );

				if ( in_array( $shipping_method['method_requires'], array(
						'min_amount',
						'either',
						'both'
					) ) && $total >= $min_amount ) {
					$has_met_min_amount = true;
				}

				if ( $shipping_method['method_requires'] == 'coupon' && ! $is_counpon_free_shipping ) {
					$enable_current_rate = false;
				} else if ( $shipping_method['method_requires'] == 'min_amount' && ! $has_met_min_amount ) {
					$enable_current_rate = false;
				} else if ( $shipping_method['method_requires'] == 'either' && ( ! $is_counpon_free_shipping && ! $has_met_min_amount ) ) {
					$enable_current_rate = false;
				} else if ( $shipping_method['method_requires'] == 'both' && ( ! ( $is_counpon_free_shipping && $has_met_min_amount ) ) ) {
					$enable_current_rate = false;
				}

			}

			return $enable_current_rate;

		}

		/**
		 * @param $costs
		 *
		 * @return array
		 */
		protected function get_taxes_per_item( $costs ) {
			$taxes = array();

			// If we have an array of costs we can look up each items tax class and add tax accordingly
			if ( is_array( $costs ) ) {

				$cart = WC()->cart->get_cart();

				foreach ( $costs as $cost_key => $amount ) {
					if ( ! isset( $cart[ $cost_key ] ) ) {
						continue;
					}

					$item_taxes = WC_Tax::calc_shipping_tax( $amount, WC_Tax::get_shipping_tax_rates( $cart[ $cost_key ]['data']->get_tax_class() ) );

					// Sum the item taxes
					foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
						$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
					}
				}

				// Add any cost for the order - order costs are in the key 'order'
				if ( isset( $costs['order'] ) ) {
					$item_taxes = WC_Tax::calc_shipping_tax( $costs['order'], WC_Tax::get_shipping_tax_rates() );

					// Sum the item taxes
					foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
						$taxes[ $key ] = ( isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
					}
				}
			}

			return $taxes;
		}

		/**
		 * @param $vendor
		 *
		 * @return bool
		 */
		private function is_coupon_free_shipping( $vendor ) {
			$discounts = new WC_Discounts( WC()->cart );

			foreach ( WC()->cart->applied_coupons as $code ) {
				$coupon = new WC_Coupon( $code );
				if ( $discounts->is_coupon_valid( $coupon ) ) {

					$coupon_author = get_post_field( 'post_author', yit_get_prop( $coupon, 'id' ) );

					if ( in_array( $coupon_author, $vendor->get_admins() ) && $coupon->get_free_shipping() ) {
						return true;
					}
				}

			}

			return false;

		}
	}
}

