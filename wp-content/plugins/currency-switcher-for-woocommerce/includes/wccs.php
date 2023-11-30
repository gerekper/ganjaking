<?php

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * This class defines wccs logic code for the plugin.
 */

if ( ! class_exists( 'WCCS' ) ) {

	class WCCS {

		private $default_currency      = null;
		private $default_currency_flag = null;
		private $currency              = null;
		private $currency_info         = array();
		private $currencies            = array();
		private $storage               = null;
		private $is_fixed              = false;
		private $selected_zone_id      = false;
		private $filter_counter        = null;
		public $wccs_priorities        = array();


		public function __construct() {

			// initialize properties
			$this->default_currency      = get_woocommerce_currency();
			$this->default_currency_flag = get_option( 'wccs_default_currency_flag', true );
			$this->currencies            = get_option( 'wccs_currencies', array() );
			$this->storage               = new WCCS_Storage( get_option( 'wccs_currency_storage', 'transient' ) );
			$this->filter_counter        = 1;

			$currency   = $this->storage->get_val( 'wccs_current_currency' );

			$currencies = $this->wccs_get_currencies();         
			if ( isset( $currencies[ $currency ] ) ) {
				$this->currency      = $currency;
				$this->currency_info = $currencies[ $currency ];
				$this->currency_info['symbol'] = get_woocommerce_currency_symbol( $currency );
			}

			$this->wccs_set_hook_priorities();

			// add admin style and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'wccs_admin_enqueue_assets' ) );

			// add frontend style and scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'wccs_front_enqueue_assets' ) );

			// change currency
			add_filter( 'woocommerce_currency', array( $this, 'wccs_woocommerce_currency' ), 9999 );

			// change currency symbol
			add_filter( 'woocommerce_currency_symbol', array( $this, 'wccs_woocommerce_currency_symbol' ), 9999, 2 );

			// for shop page
			add_filter( 'woocommerce_currency_symbol', array( $this, 'wccs_order_page_currency' ), 999, 2 );

			// format price based on currency
			add_filter( 'woocommerce_price_format', array( $this, 'wccs_price_format' ), 999, 2 );

			// add decimals based on currency
			add_filter( 'wc_price_args', array( $this, 'wccs_price_args' ), 999 );

			// override price
			add_filter( 'woocommerce_product_get_price', array( $this, 'wccs_custom_price' ), $this->wccs_priorities['woocommerce_product_get_price'], 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( $this, 'wccs_custom_price' ), $this->wccs_priorities['woocommerce_product_get_price'], 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( $this, 'wccs_custom_price' ), $this->wccs_priorities['woocommerce_product_get_price'], 2 );

			// We use tier pricing filter to back price to it orignal value.
			add_filter( 'tier_pricing_table/cart/product_cart_price', array( $this, 'wccs_tier_pricing' ), 99, 2 );

			// override prices for subscriptions schemes all product subscription somewherewarm
			add_filter( 'wcsatt_single_product_subscription_option_data', array( $this, 'wccs_wcsatt_subscription_scheme_prices' ), 99, 3 );            

			// woocommerce booking
			// add_filter('woocommerce_bookings_calculated_booking_cost', array($this, 'woocommerce_booking_product_price'), 999, 3);
			add_filter( 'woocommerce_bookings_calculated_booking_cost_success_output', array( $this, 'woocommerce_booking_product_price_string' ), 999, 3 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'woocommerce_booking_product_price_html' ), 9, 2 );

			// Variations
			add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'wccs_custom_variable_price' ), 99, 2 );
			// add_filter('woocommerce_product_variation_get_sale_price', array($this, 'wccs_custom_variable_price'), 9, 2);
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'wccs_custom_variable_price' ), 99, 2 );

			// woocommerce subscription products
			add_filter( 'woocommerce_subscriptions_product_price', array( $this, 'wccs_subscription_product_price' ), 99, 2 );
			add_filter( 'woocommerce_subscriptions_product_sign_up_fee', array( $this, 'wccs_subscription_product_price_signup' ), 99, 2 );

			// Variable (price range)
			add_filter( 'woocommerce_variation_prices_price', array( $this, 'wccs_custom_variation_price' ), $this->wccs_priorities['woocommerce_variation_prices_price'], 3 );
			add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'wccs_custom_variation_price' ), $this->wccs_priorities['woocommerce_variation_prices_price'], 3 );
			add_filter( 'woocommerce_variation_prices_sale_price', array( $this, 'wccs_custom_variation_price' ), $this->wccs_priorities['woocommerce_variation_prices_price'], 3 );

			// Handling price caching
			add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'wccs_add_user_to_variation_prices_hash' ), 999, 1 );

			// detect currency
			add_action( 'template_redirect', array( $this, 'wccs_detect_currency' ) );

			// add switcher shortcode
			add_shortcode( 'wcc_switcher', array( $this, 'wcc_switcher_shortcode_callback' ) );

			// add rates shortcode
			add_shortcode( 'wcc_rates', array( $this, 'wcc_rates_shortcode_callback' ) );

			// register widgets
			add_action( 'widgets_init', array( $this, 'wccs_widgets' ) );

			add_filter( 'wp_get_nav_menu_items', array( $this, 'wccs_get_nav_menu_items_filter' ), 999, 3 );

			// add sticky switcher
			add_action( 'wp_enqueue_scripts', array( $this, 'wccs_add_sticky_callback' ) );

			// override shipping price
			add_filter( 'woocommerce_package_rates', array( $this, 'wccs_change_shipping_rates_cost' ), 10, 2 );

			// change currency on checkout before creating order.
			// This will work according to backend value set by user whether to pay in user currency or default currency
			add_filter( 'woocommerce_checkout_create_order', array( $this, 'wccs_change_order_currency' ), 999, 1 );

			// Change currency on manual order create from admin
			add_action('woocommerce_new_order', array( $this, 'wccs_creating_order_from_admin' ), 999, 1);

			add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'wccs_nonce_checkout_field' ), 10 );

			// calculate shipping price
			add_action( 'woocommerce_checkout_create_order_shipping_item', array( $this, 'wccs_checkout_create_order_shipping_item' ), 10, 4 );

			// adding meta box to order page wp admin
			add_action( 'add_meta_boxes', array( $this, 'wccs_register_order_meta_box' ) );

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'wccs_change_wc_gateway_if_empty' ), 999, 1 );          

			add_action( 'woocommerce_coupon_loaded', array( $this, 'wccs_woocommerce_coupon_loaded' ), 9999 );

			add_action( 'wp_ajax_wccs_update_currency_by_billing_country', array( $this, 'wccs_update_currency_by_billing_country' ) );
			add_action( 'wp_ajax_nopriv_wccs_update_currency_by_billing_country', array( $this, 'wccs_update_currency_by_billing_country' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'wccs_checkout_scripts' ) );

			add_action( 'wccs_detect_wpml_lang', array( $this, 'wccs_detect_wpml_lang' ) );

			add_action( 'wccs_zone_pricing', array( $this, 'wccs_zone_pricing' ) );
		
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'wccs_refresh_cart_subtotal' ) );

			add_filter( 'wcs_cart_totals_order_total_html', array( $this, 'wccs_cart_totals_order_total_html' ), 999, 2 );

			add_action('wp_ajax_wccs_currency_to_default', array( $this, 'wccs_currency_to_default' ));
			add_action('wp_ajax_nopriv_wccs_currency_to_default', array( $this, 'wccs_currency_to_default' ));
		}

		public function wccs_currency_to_default() {
			$this->currency      = sanitize_text_field( $this->default_currency );
			$currencies = $this->wccs_get_currencies();
			$this->currency_info = isset($currencies[ sanitize_text_field( $this->default_currency ) ]) ? $currencies[ sanitize_text_field( $this->default_currency ) ] : $this->currency_info;

			// set storage to default currency
			$this->storage->set_val( 'wccs_current_currency', $this->currency );
			$this->storage->set_val( 'set_by_location_currency', $this->currency );

			echo 'success';
			wp_die();
		}

		/**
		 *  We have explode the total recurring amount and pass cart total and glued them. 
		 */
		public function wccs_cart_totals_order_total_html( $order_total_html, $cart ) {    

			$str = explode(' / ', $order_total_html);
			$str[0] = '<strong>' . WC()->cart->get_total() . '</strong>';
			$str = implode(' / ', $str);            
			return $str;
		}

		public function wccs_creating_order_from_admin( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( is_admin() ) {
				if ( ! empty( $this->wccs_get_currency() ) ) {
					if ( $order ) {
						$order->update_meta_data( '_order_currency' , $this->wccs_get_currency() );
						$order->save();
					}
					// update_post_meta( $order_id, '_order_currency', $this->wccs_get_currency() );
				}               
			}
		}

		public function is_early_renew_subscription( $for_price = false ) { 

			global $woocommerce;
			if (isset($_GET['user_id'])) {
				if ( ! empty( $woocommerce->cart ) && ! empty( $woocommerce->cart->get_cart() ) ) {
					foreach ( $woocommerce->cart->get_cart() as $key => $value ) {
						if ( isset( $value['subscription_renewal'] ) && isset( $value['subscription_renewal']['subscription_renewal_early'] ) ) {   
							if ( $value['subscription_renewal']['subscription_renewal_early'] ) {                   
								return 'stop';
							}
						}
					}
				}
			}
		}

		public function wccs_refresh_cart_subtotal( $fragments ) {

			ob_start();
			
			// global $woocommerce;

			if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
				define( 'WOOCOMMERCE_CART', true );
			}
			
			WC()->cart->calculate_totals();

			echo wp_kses_post( wc_price( WC()->cart->get_cart_total() ) );

			$fragments['a.cart-contents'] = ob_get_clean();
			return $fragments;
		}

		public function wccs_zone_pricing() {

			//remove currency by location first
			$this->storage->remove_val( 'set_by_location_currency' );
			$this->storage->remove_val( 'wccs_current_currency' );

			$data = wccs_get_client_ip_server();
			$flag = false;
			$selected_currency = '';
			$selected_rate = '';
			$selected_currency_symbol = '';
			$selected_decimal = 0;
			
			// echo '<pre>$data$data';
			//  print_r( $data );
			// echo '</pre>';           

			// Get existing values from db.
			$wccs_zp_data = get_option( 'wccs_zp_data', false );
			if ( false != $wccs_zp_data && count( $wccs_zp_data ) > 0 ) {
				// search for location country in zone.
				foreach ( $wccs_zp_data as $key => $zp_data ) {
					if ( isset( $zp_data['zp_name'] ) && isset( $data['geoplugin_countryCode'] ) ) {
						$needle = $data['geoplugin_countryCode'];
						$hay = $zp_data['zp_countries'];
						if ( in_array( $needle, $hay ) ) {
							$flag = true;
							$this->selected_zone_id  = $key;
							//echo 'id is ' . $this->selected_zone_id;
							//die('zone id ');
							$selected_currency = $zp_data['zp_currency'];
							$selected_rate = $zp_data['zp_rate'];
							$selected_decimal = $zp_data['zp_decimal'];
							$selected_currency_symbol = get_woocommerce_currency_symbol( $selected_currency );
							// echo '<pre> at index ' . $key;
							// print_r( $zp_data );
							// echo '</pre>';
							break;
						}
					}
				}

				//search for all in zone
				foreach ( $wccs_zp_data as $key => $zp_data ) {
					if ( isset( $zp_data['zp_name'] ) ) {
						$hay = $zp_data['zp_countries'];
						if ( in_array( 'ALL', $hay ) ) {
							//echo 'I am in all countires';
							$flag = true;
							$this->selected_zone_id  = $key;
							//echo 'id is ' . $this->selected_zone_id;
							//die('zone id ');
							$selected_currency = $zp_data['zp_currency'];
							$selected_rate = $zp_data['zp_rate'];
							$selected_decimal = $zp_data['zp_decimal'];
							$selected_currency_symbol = get_woocommerce_currency_symbol( $selected_currency );
							// echo '<pre> at index ' . $key;
							// print_r( $zp_data );
							// echo '</pre>';
							break;
						}
					}
				}
				
				// echo '<pre>$wccs_zp_data$wccs_zp_data';
				//  print_r( $wccs_zp_data );
				// echo '</pre>';
			}

			if ( $flag && '' != $selected_currency && '' != $selected_rate && '' != $selected_currency_symbol ) {
				
				// $currencies = $this->wccs_get_currencies();
				// echo 'selected currency is ' . $selected_currency;
				// $currencies[ $selected_currency ] = array(
				//  'zone_id' => $this->selected_zone_id,
				//  'rate' => $selected_rate,
				//  'symbol' => $selected_currency_symbol
				// );               

				//$this->currency_info = array();
				$this->currency      = $selected_currency;
				$this->currency_info['zone_id'] = $this->selected_zone_id;
				$this->currency_info['rate'] = $selected_rate;
				$this->currency_info['decimals'] = $selected_decimal;
				$this->currency_info['symbol'] = $selected_currency_symbol;

				// echo 'selected currency is ' . $selected_currency . '<br>';
				// echo 'selected currency symbol is ' . $selected_currency_symbol . '<br>';
				// echo 'selected currency rate is ' . $selected_rate . '<br>';
				// echo 'selected currency id is ' . $this->selected_zone_id . '<br>';

				// echo '<pre>$this->currency_info$this->currency_info';
				// print_r( $this->currency_info );
				// echo '</pre>';

				// // set storage
				$this->storage->set_val( 'wccs_current_currency', $this->currency );
			
			} else {
				$this->currency      = null;
				$this->currency_info = array();

				// remove storage
				$this->storage->remove_val( 'wccs_current_currency' );
			}           

			return false;   
		}

		public function wccs_detect_wpml_lang() {
			
			if ( function_exists( 'icl_get_languages' ) && defined( 'ICL_LANGUAGE_CODE' ) && '' != ICL_LANGUAGE_CODE ) {

				//remove currency by location first
				$this->storage->remove_val( 'set_by_location_currency' );
				$this->storage->remove_val( 'wccs_current_currency' );

				$wccs_lang = get_option( 'wccs_lang' );
				// echo '<pre>$wccs_lang$wccs_lang';
				// print_r( $wccs_lang );
				// echo '</pre>';
				//wp_die('wroking!');

				if ( isset( $wccs_lang[ ICL_LANGUAGE_CODE ] ) && ! empty( $wccs_lang[ ICL_LANGUAGE_CODE ] ) ) {
					$currency_code = $wccs_lang[ ICL_LANGUAGE_CODE ];
								
					$currencies = $this->wccs_get_currencies();

					if ( isset( $currencies[ $currency_code ] ) ) {

						$this->currency      = $currency_code;
						$this->currency_info = $currencies[ $currency_code ];

						// set storage
						$this->storage->set_val( 'wccs_current_currency', $this->currency );

					} else {

						$this->currency      = null;
						$this->currency_info = array();

						// remove storage
						$this->storage->remove_val( 'wccs_current_currency' );

					}
					
				} else {
					$this->currency      = null;
					$this->currency_info = array();

					// remove storage
					$this->storage->remove_val( 'wccs_current_currency' );
				}

				return false;
			}
		}

		public function wccs_checkout_scripts() {
			wp_register_script( 'wccs_checkout', WCCS_PLUGIN_URL . 'assets/frontend/js/wccs_checkout.js', array( 'jquery' ), '1.5.5&t=' . gmdate( 'his' ) );
			wp_localize_script(
				'wccs_checkout',
				'wccs_checkout',
				array(
					'admin_url' => admin_url( 'admin-ajax.php' ),
					'nonce'     => wp_create_nonce( 'wccs_update_currency_by_billing_country' ),
					'action'    => 'wccs_update_currency_by_billing_country',
					'is_shop_currency' => get_option( 'wccs_pay_by_user_currency' ),
					'is_billing_currency' => get_option( 'wccs_currency_by_billing' ),
					'shop_currency' => get_option('wccs_pay_by_user_currency', false),
				)
			);

			wp_register_script( 'wccs_early_renewal_subscription', WCCS_PLUGIN_URL . 'assets/frontend/js/wccs_early_subscription.js', array( 'jquery' ), '1.5.5&t=' . gmdate( 'his' ) );
			wp_localize_script(
				'wccs_early_renewal_subscription',
				'wccs_early_renewal_subscription',
				array(
					'admin_url' => admin_url( 'admin-ajax.php' ),
					'nonce'     => wp_create_nonce( 'wccs_early_renewal_subscription' ),
					'action'    => 'wccs_check_currency_before_early_renew',                
				)
			);

			if ( is_account_page() ) {
				wp_enqueue_script( 'wccs_early_renewal_subscription' );
			}

			if ( is_checkout() ) {
				wp_enqueue_script( 'wccs_checkout' );
			}
		}

		public function wccs_update_currency_by_billing_country() {
			
			if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), sanitize_text_field( $_POST['action'] ) ) ) {
				exit( 'unauthorized!' );
			}           

			$result = array();
			$result['status'] = 'fail';
			$result['url'] = '';

			if ( '1' == get_option( 'wccs_zp_toggle' ) ) {
				echo json_encode( $result );
				wp_die();
			}

			$currencies       = get_option( 'wccs_currencies', array() );
			$currencies[ $this->wccs_get_default_currency() ] = array();
			$current_currency = ! empty( $this->wccs_get_currency() ) ? $this->wccs_get_currency() : $this->wccs_get_default_currency();
			if ( ! empty( $_POST['billing_currency'] ) ) {
				$currency_by_posted_country_code = wccs_get_country_currency( sanitize_text_field( $_POST['billing_currency'] ) );
				if ( ! empty( $currency_by_posted_country_code ) && ! empty( $current_currency ) && isset( $currencies[ $currency_by_posted_country_code ] ) && $currency_by_posted_country_code != $current_currency ) {
					$url = wc_get_checkout_url() . '?wcc_switcher=' . $currency_by_posted_country_code;
					$result['url'] = $url;
					$result['status'] = 'success';
					echo json_encode( $result );
				} else {
					echo json_encode( $result );
				}
			} else {
				echo json_encode( $result );
			}

			wp_die();
		}

		public function wccs_woocommerce_coupon_loaded( $coupon ) {

			if ( is_admin() ) {
				return $coupon;
			}

			$coupon_id            = $coupon->get_id();
			$prices               = array();
			$prices['amount']     = $coupon->get_amount();
			$prices['min_spend']  = $coupon->get_minimum_amount();
			$prices['max_spend']  = $coupon->get_maximum_amount();

			/* converting coupon amount to the selected currency, if not percent type coupon*/
			if ( ! $coupon->is_type( 'percent_product' ) && ! $coupon->is_type( 'percent' ) ) {
				$rate = $this->wccs_get_currency_rate();
				if ( $rate ) {
					$decimals = $this->wccs_get_currency_decimals();                    
					if ( isset( $prices['amount'] ) && ! empty( $prices['amount'] ) ) {
						$prices['amount'] = round( ( $prices['amount'] * $rate ), $decimals );
					}

					if ( isset( $prices['min_spend'] ) && ! empty( $prices['min_spend'] ) ) {
						$prices['min_spend'] = round( ( $prices['min_spend'] * $rate ), $decimals );
					}

					if ( isset( $prices['max_spend'] ) && ! empty( $prices['max_spend'] ) ) {
						$prices['max_spend'] = round( ( $prices['max_spend'] * $rate ), $decimals );
					}

					$coupon->set_minimum_amount( $prices['min_spend'] );
					$coupon->set_maximum_amount( $prices['max_spend'] );
					$coupon->set_amount( $prices['amount'] );
				}
			}

			/* Fixed coupon starts from here*/          
			$currencies           = get_option( 'wccs_currencies', array() );
			$current_currency     = ! empty( $this->wccs_get_currency() ) ? $this->wccs_get_currency() : $this->wccs_get_default_currency();
			$wccs_cfa_data        = get_post_meta( $coupon_id, 'wccs_cfa_data', true );
			$wccs_cfa_minmax_data = get_post_meta( $coupon_id, 'wccs_cfa_minmax_data', true );

			if ( ! get_option( 'wccs_fixed_coupon_amount' ) || ! get_option( 'wccs_pay_by_user_currency' ) ) { // If fixed amount for coupon setting is disable OR shop by user currency is disable.
				return $coupon;
			}

			if ( ! $coupon->is_type( 'percent_product' ) && ! $coupon->is_type( 'percent' ) ) {
				$this->is_fixed = true;
			}

			if ( ! $this->is_fixed || empty( $current_currency ) || ! isset( $currencies[ $current_currency ] ) || $this->wccs_get_default_currency() == $current_currency ) {
				return $coupon;
			}

			foreach ( $prices as $key => $value ) {

				if ( 'amount' == $key && $this->is_fixed ) {
					if ( ! empty( $wccs_cfa_data ) && isset( $wccs_cfa_data[ $current_currency ] ) ) {
						$temp_amount = floatval( $wccs_cfa_data[ $current_currency ] );
						if ( '' != $temp_amount && 0 <= $temp_amount ) {
							$prices['amount'] = $temp_amount;
						}
					}
				}

				if ( 'min_spend' == $key && $this->is_fixed ) {
					if ( ! empty( $wccs_cfa_minmax_data ) && isset( $wccs_cfa_minmax_data[ $current_currency ]['min'] ) ) {
						$temp_min_amount = floatval( $wccs_cfa_minmax_data[ $current_currency ]['min'] );
						if ( '' != $temp_min_amount && 0 <= $temp_min_amount ) {
							$prices['min_spend'] = $temp_min_amount;
						}
					}
				}

				if ( 'max_spend' == $key && $this->is_fixed ) {
					if ( ! empty( $wccs_cfa_minmax_data ) && isset( $wccs_cfa_minmax_data[ $current_currency ]['max'] ) ) {
						$temp_max_amount = floatval( $wccs_cfa_minmax_data[ $current_currency ]['max'] );
						if ( '' != $temp_max_amount && 0 <= $temp_max_amount ) {
							$prices['max_spend'] = $temp_max_amount;
						}
					}
				}
			}

			$coupon->set_minimum_amount( $prices['min_spend'] );
			$coupon->set_maximum_amount( $prices['max_spend'] );
			$coupon->set_amount( $prices['amount'] );

			return $coupon;
		}

		public function wccs_wcsatt_subscription_scheme_prices( $option_data, $subscription_scheme, $product ) {

			$rate = $this->wccs_get_currency_rate();

			if ( 0 <= $this->selected_zone_id ) {   
				$individual_rate = trim(get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $this->selected_zone_id, true ));
				if ( '' != $individual_rate && 0 != $individual_rate ) {
					$rate = $individual_rate;                           
				}
			}

			if ( $rate ) {

				$decimals = $this->wccs_get_currency_decimals();

				if ( ! empty( $option_data['subscription_scheme']['regular_price'] ) ) {
					$option_data['subscription_scheme']['regular_price'] = round( ( $option_data['subscription_scheme']['regular_price'] * $rate ), $decimals );
					$option_data['subscription_scheme']['regular_price'] = $this->wccs_get_currency_rounding($option_data['subscription_scheme']['regular_price']);
					$option_data['subscription_scheme']['regular_price'] = $this->wccs_get_currency_charming($option_data['subscription_scheme']['regular_price']);
				}

				if ( ! empty( $option_data['subscription_scheme']['sale_price'] ) ) {
					$option_data['subscription_scheme']['sale_price'] = round( ( $option_data['subscription_scheme']['sale_price'] * $rate ), $decimals );
					$option_data['subscription_scheme']['sale_price'] = $this->wccs_get_currency_rounding($option_data['subscription_scheme']['sale_price']);
					$option_data['subscription_scheme']['sale_price'] = $this->wccs_get_currency_charming($option_data['subscription_scheme']['sale_price']);
				}

				if ( ! empty( $option_data['subscription_scheme']['price'] ) ) {
					$option_data['subscription_scheme']['price'] = round( ( $option_data['subscription_scheme']['price'] * $rate ), $decimals );
					$option_data['subscription_scheme']['price'] = $this->wccs_get_currency_rounding($option_data['subscription_scheme']['price']);
					$option_data['subscription_scheme']['price'] = $this->wccs_get_currency_charming($option_data['subscription_scheme']['price']);
				}
			}

			return $option_data;
		}

		public function wccs_order_page_currency( $symbol, $currency ) {

			if ( is_admin() ) {             

				$post_id = isset( $_GET['post'] ) && ! empty( sanitize_text_field( $_GET['post'] ) ) ? sanitize_text_field( $_GET['post'] ) : '';               
				if ( 'shop_order' === get_post_type( $post_id ) ) {
					if ( 'iso-code' === get_option( 'wccs_currency_display', 'symbol' ) ) {                     
						return $currency;
					} else {
						$currencies = $this->wccs_get_currencies();
						$prefix     = null;
						if ( isset( $currencies[ $currency ] ) ) {
							$info = $currencies[ $currency ];
							if ( isset( $info['symbol_prefix'] ) ) {
								$prefix = $info['symbol_prefix'];
							}
						}



						return $prefix . $symbol;
					}
				}
			}

			return $symbol;
		}

		private function wccs_set_hook_priorities() {
			$priorities = array(
				'woocommerce_product_get_price'         => 99,
				'woocommerce_variation_prices_price'    => 99,
			);
			/**
			* Filter wccs_hook_priorities
			* 
			* @since 1.0
			**/
			$this->wccs_priorities = apply_filters( 'wccs_hook_priorities', $priorities );

			if ( empty( $this->wccs_priorities ) ) {
				$this->wccs_priorities = $priorities;
			}
		}

		public function woocommerce_booking_product_price_string( $output, $display_price, $product ) {

			$output = wccs_delete_all_between( '<span class="woocommerce-Price-currencySymbol">', '</span>', $output );

			$output_in_arr = explode( '<strong>', $output );
			$text          = $output_in_arr[0];
			$output_in_arr = explode( '</strong>', $output_in_arr[1] );

			$price = $output_in_arr[0];

			$rate = $this->wccs_get_currency_rate();
			
			if ( 0 <= $this->selected_zone_id ) {   
				$individual_rate = trim(get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $this->selected_zone_id, true ));
				if ( '' != $individual_rate && 0 != $individual_rate ) {
					$rate = $individual_rate;                           
				}
			}

			if ( is_numeric( $display_price ) && $rate ) {

				$decimals = $this->wccs_get_currency_decimals();
				$price    = round( ( $display_price * $rate ), $decimals );
				$price = $this->wccs_get_currency_rounding($price);
				$price = $this->wccs_get_currency_charming($price);
				$price    = '<span class="woocommerce-Price-amount amount"><bdi>' . $price . '</bdi></span>';
			}

			if ( 'left_space' == $this->currency_info['format'] ) {
				$output = $text . '<strong><span class="woocommerce-Price-currencySymbol">' . $this->currency_info['symbol'] . '</span> ' . $price . '</strong>';
			} elseif ( 'right_space' == $this->currency_info['format'] ) {
				$output = $text . '<strong>' . $price . ' <span class="woocommerce-Price-currencySymbol">' . $this->currency_info['symbol'] . '</span></strong>';
			} elseif ( 'left' == $this->currency_info['format'] ) {
				$output = $text . '<strong><span class="woocommerce-Price-currencySymbol">' . $this->currency_info['symbol'] . '</span>' . $price . '</strong>';
			} else {
				$output = $text . '<strong>' . $price . '<span class="woocommerce-Price-currencySymbol">' . $this->currency_info['symbol'] . '</span></strong>';
			}

			// $selected_currency =

			return $output;
		}

		public function woocommerce_booking_product_price_html( $output, $product ) {

			$prod_type = $product->get_type();

			if ( 'booking' === $prod_type ) {

				$output = wccs_delete_all_between( '<del>', '</del>', $output );
			}

			return $output;
		}

		public function wccs_change_wc_gateway_if_empty( $allowed_gateways ) {

			if ( ! is_admin() && isset( $this->currency_info['payment_gateways'] ) && ! empty( $this->currency_info['payment_gateways'] ) ) {

				foreach ( $this->currency_info['payment_gateways'] as $active_payment_gateway ) {

					unset( $allowed_gateways[ $active_payment_gateway ] );

				}
			}
			return $allowed_gateways;
		}

		/**
		 * Function to return value in exchange rate selected currently..
		 *
		 * @param price
		 * @return exchange_price
		 */
		public function wccs_price_conveter( $price = '', $curr = false ) {

			if ( empty( $price ) ) {
				return;
			}

			$detect_currency = $this->storage->get_val( 'wccs_current_currency' );

			// price will remain same
			if ( empty( $detect_currency ) && '1' !== get_option( 'wccs_currency_by_location' ) && '1' !== get_option( 'wccs_show_in_menu' ) && '1' !== get_option( 'wccs_sticky_switcher' ) ) {
				$price = $price;
			} else {
				$coversion_rate = $this->wccs_get_currency_rate();
				$decimals       = $this->wccs_get_currency_decimals();
				if ( empty( $coversion_rate ) ) {
					$price = $price;
				} else {
					$price = round( ( $price * $coversion_rate ), $decimals );
				}
			}

			if ( false === $curr ) {
				return $price;
			} else {
				return wc_price( $price );
			}
		}

		public function wccs_register_order_meta_box() {
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				global $theorder;
				$order = $theorder;

				// get_post_meta( $post->ID, '_wccs_shop_currency', true );
				if ( isset ( $order ) && ! empty( $order->get_meta( '_wccs_shop_currency' , true) ) ) {
					add_meta_box( 'wccs-currency-metabox', esc_html__( 'WCCS Order Info', 'wccs' ), array( $this, 'wccs_render_order_metabox_html' ), '', 'side', 'core' );
				}
			} else {
				global $post;
				if ( ! empty( get_post_meta( $post->ID, '_wccs_shop_currency', true ) ) ) {
					add_meta_box( 'wccs-currency-metabox', esc_html__( 'WCCS Order Info', 'wccs' ), array( $this, 'wccs_render_order_metabox_html' ), '', 'side', 'core' );
				}
			}
		}

		public function wccs_render_order_metabox_html() {
			global $theorder;
			$order = $theorder;

			printf( esc_html( '%s %s' ) . esc_html__( 'Order Currency:', 'wccs' ) . esc_html( '%s ' . $order->get_meta( '_wccs_shop_currency' , true) . '%s' ), '<p>', '<strong>', '</strong>', '</p>' );
			printf( esc_html( '%s %s' ) . esc_html__( 'Base Currency:', 'wccs' ) . esc_html( '%s ' . $order->get_meta( '_wccs_base_currency' , true) . '%s' ), '<p>', '<strong>', '</strong>', '</p>' );
			printf( esc_html( '%s %s' ) . esc_html__( 'Order Currency rate:', 'wccs' ) . esc_html( '%s ' . $order->get_meta( '_wccs_currency_rate' , true) . '%s' ), '<p>', '<strong>', '</strong>', '</p>' );
			printf( esc_html( '%s %s' ) . esc_html__( 'Total Amount:', 'wccs' ) . esc_html( '%s ' . $order->get_meta( '_wccs_total_in_base_currency' , true) . '%s' ), '<p>', '<strong>', '</strong>', '</p>' );
		}

		public function wccs_nonce_checkout_field() {
			wp_nonce_field( '_wccsnonce', '_wccsnonce' );
		}

		public function wccs_checkout_create_order_shipping_item( $item, $package_key, $package, $order = '' ) {
			/**
			 * Filter
			 * 
			 * @since 1.0.0
			 */
			$surpass = apply_filters( 'wccs_surpass_shipping_conversion', true );           

			if ( ! $surpass ) {

				if ( ! empty( $this->wccs_get_currency() ) ) {
					if ( ! get_option( 'wccs_pay_by_user_currency' ) && $this->wccs_get_currency() != $this->wccs_get_default_currency() ) {                        

						$coversion_rate  = $this->wccs_get_currency_rate();
						$decimals        = $this->wccs_get_currency_decimals();
						$rates           = $package['rates'];
						$shipping_total  = 0;
						$data            = wcc_get_post_data();
						$shipping_method = $data['shipping_method'][0];
						foreach ( $rates as $id => $rate ) {
							// echo $id;
							if ( isset( $rates[ $id ] ) ) {

								if ( $coversion_rate && $shipping_method === $id ) {
									$decimals       = $this->wccs_get_currency_decimals();
									$shipping_total = $rates[ $id ]->cost;
									break;
								}
							}
						}                       

						$shipping_total = round( ( $shipping_total / $coversion_rate ), $decimals );
						$item->set_total( $shipping_total );
						// Make new taxes calculations
						$item->calculate_taxes();
						$item->save();
						$order->calculate_totals();
					}
				}
			}
		}

		public function wccs_change_order_currency( $order ) {

			if ( ! empty( $this->wccs_get_currency() ) ) {
				
				if ( '1' == get_option( 'wccs_pay_by_user_currency' ) ) {
					$order_total    = $order->get_total();
					$coversion_rate = $this->wccs_get_currency_rate();
					$decimals       = $this->wccs_get_currency_decimals();
					// update order meta data
					$order->update_meta_data( '_wccs_shop_currency', $this->wccs_get_currency() );
					$order->update_meta_data( '_wccs_base_currency', $this->wccs_get_default_currency() );
					$order->update_meta_data( '_wccs_currency_rate', $coversion_rate );
					$order->update_meta_data( '_wccs_total_in_base_currency', round( ( $order_total / $coversion_rate ), $decimals ) );
				}
			}

			return $order;
		}

		public function wccs_change_shipping_rates_cost( $rates, $package ) {

			if ( 1 == $this->filter_counter ) { 
				$this->filter_counter++;
				$coversion_rate = $this->wccs_get_currency_rate();
				$decimals       = $this->wccs_get_currency_decimals();

				if ( $coversion_rate ) {
					foreach ( $rates as $id => $rate ) {

						if ( isset( $rates[ $id ] ) ) {

							$rates[ $id ]->cost = round( ( $rates[ $id ]->cost * $coversion_rate ), $decimals );

							// Taxes rate cost (if enabled)
							$taxes = array();
							foreach ( $rates[ $id ]->taxes as $key => $tax ) {
								if ( $tax > 0 ) { // set the new tax cost
									// set the new line tax cost in the taxes array
									$taxes[ $key ] = round( ( $tax * $coversion_rate ), $decimals );
								}
							}
							// Set the new taxes costs
							$rates[ $id ]->taxes = $taxes;
						}
					}
				}

				return $rates;
			}

			return $rates;
		}

		public function wccs_admin_enqueue_assets( $hook ) {

			if ( 'woocommerce_page_wccs-settings' == $hook || 'shop_coupon' == get_post_type() || 'product' == get_post_type() ) {
				wp_enqueue_style( 'wccs_admin_settings_style', WCCS_PLUGIN_URL . 'assets/admin/css/setting_style.css', '', '1.0&t=' . gmdate('dmYhis') );
			}

			if ( 'woocommerce_page_wccs-settings' == $hook || 'shop_coupon' == get_post_type() ) {
				wp_enqueue_style( 'wccs_jquery_ui_css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css', '', '1.0' );
				// wp_enqueue_style('wccs_select2_style', "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css");
				wp_enqueue_style( 'wccs_flags_style', WCCS_PLUGIN_URL . 'assets/lib/flag-icon/flag-icon.css', '', '1.0' );
				wp_enqueue_style( 'wccs_pretty_dd_style', WCCS_PLUGIN_URL . 'assets/lib/pretty_dropdowns/prettydropdowns.css', '', '1.0' );
				
				wp_enqueue_style( 'wccs_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), WCCS_VERSION . '?t=' . gmdate( 'His' ) );

				wp_enqueue_script( 'wccs_jquery_ui_script', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array( 'jquery' ), '1.0' );
				// wp_enqueue_script('wccs_select2_script', "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js", array('jquery'));
				wp_enqueue_script( 'wccs_select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array( 'jquery' ), WCCS_VERSION . '?t=' . gmdate( 'His' ), true );
				wp_enqueue_script( 'wccs_pretty_dd_script', WCCS_PLUGIN_URL . 'assets/lib/pretty_dropdowns/jquery.prettydropdowns.js', array( 'jquery' ), '1.0' );
				wp_enqueue_script( 'wccs_admin_settings_script', WCCS_PLUGIN_URL . 'assets/admin/js/setting_script.js', array( 'wccs_jquery_ui_script' ), '1.0&t=' . gmdate( 'His' ) );
				wp_localize_script(
					'wccs_admin_settings_script',
					'variables',
					array(
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'flags_placeholder' => __( 'Choose Flag', 'wccs' ),
						'nonce' => wp_create_nonce('wccs'),
					)
				);
			}
		}

		public function wccs_front_enqueue_assets() {
			if ( get_option( 'wccs_show_in_menu', 0 ) ) {
				wp_enqueue_style( 'wccs_flags_style', WCCS_PLUGIN_URL . 'assets/lib/flag-icon/flag-icon.css', '', '1.0' );
				wp_enqueue_style( 'wccs_menu_style', WCCS_PLUGIN_URL . 'assets/frontend/css/menu_style.css', '', '1.0' );
				wp_enqueue_script( 'wccs_menu_script', WCCS_PLUGIN_URL . 'assets/frontend/js/menu_script.js', array( 'jquery' ), '1.0' );
			}
		}

		public function wccs_woocommerce_currency( $currency ) {
			if ( ! is_admin() || wp_doing_ajax() ) {                
				//echo( 'mufaddal ' . $this->wccs_get_currency() );
				if ( $this->wccs_get_currency() ) {
					return $this->wccs_get_currency();
				}
			}

			return $currency;
		}

		public function wccs_woocommerce_currency_symbol( $symbol, $currency ) {
			if ( ! is_admin() || wp_doing_ajax() ) {

				$this->wccs_get_currency();

				if ( 'iso-code' === get_option( 'wccs_currency_display', 'symbol' ) ) {
					return $currency;
				}

				if ( $currency != $this->wccs_get_default_currency() ) {
					if ( $this->wccs_get_currency_symbol() ) {
						return $this->wccs_get_currency_symbol();
					}
				}
			}

			return $symbol;
		}

		public function wccs_price_format( $format, $currency_pos ) {

			$woo_default_currency = get_option( 'woocommerce_currency' );
			$current_currency     = $this->wccs_get_currency();

			if ( ( ! is_admin() && ! empty( $current_currency ) ) || ( wp_doing_ajax() && ! empty( $current_currency ) ) ) {
				$get_symbol_position    = $this->wccs_get_currency_format();
				if ( '' != $get_symbol_position ) {
					$current_pos = $get_symbol_position;
				} else {
					$current_pos = $currency_pos;
				}
				
				$default_format = $current_pos;

				switch ( $current_pos ) {
					case 'left':
						$format = '%1$s%2$s';
						break;
					case 'right':
						$format = '%2$s%1$s';
						break;
					case 'left_space':
						$format = '%1$s&nbsp;%2$s';
						break;
					case 'right_space':
						$format = '%2$s&nbsp;%1$s';
						break;
					default:
						$format       = $default_format;
						$currency_pos = $current_pos;
				}
				/**
				 * Filter
				 * 
				 * @since 1.0.0
				 */
				return apply_filters( 'wccs_price_format', $format, $currency_pos );

			} else {
				/**
				 * Filter
				 * 
				 * @since 1.0.0
				 */
				return apply_filters( 'wccs_price_format', $format, $currency_pos );
			}

			// return $format;
		}

		public function wccs_price_args( $args ) {
			if ( ! is_admin() || wp_doing_ajax() ) {
				$decimals = $this->wccs_get_currency_decimals();

				$args['decimals'] = $decimals;
			}

			return $args;
		}

		public function wccs_custom_price( $price, $product ) {         

			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}

				if ( 'donation' == get_post_meta( $product->get_id(), 'is_wc_donation', true ) ) {
					return $price;
				}

				$rate = $this->wccs_get_currency_rate();                    
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;

				wc_delete_product_transients( $product->get_id() );

				//print_r( $current_currency_info );
				
				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {                       
					$individual_rate = trim( get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ) );
					if ( '' != $individual_rate && 0 != $individual_rate ) {                            
						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                            
						return $price;                  
					}
				}

				if ( $rate ) {              
					$price = round( ( $price * $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);
				}
			}           

			return $price;
		}

		public function wccs_tier_pricing( $price, $cart_item ) {           

			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}

				$rate = $this->wccs_get_currency_rate();
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;
				//print_r( $current_currency_info );

				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {
					$individual_rate = trim(get_post_meta( $cart_item['product_id'], 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ));
					if ( '' != $individual_rate && 0 != $individual_rate ) {
						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                            
						return $price;                  
					}
				}

				if ( $rate ) {
					$price = round( ( $price / $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);
				}
			}

			return $price;
		}

		public function wccs_custom_variable_price( $price, $variation ) {          

			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}

				$rate = $this->wccs_get_currency_rate();
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;
				$individual_rate = '';  
				wc_delete_product_transients( $variation->get_id() );       

				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {

					// echo '<pre>$current_currency_info$current_currency_info';
					//  print_r( $current_currency_info );
					// echo '</pre>';

					$individual_rate = trim(get_post_meta( $variation->get_parent_id(), 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ));
					if ( '' != $individual_rate && 0 != $individual_rate ) {

						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                        
						return $price;                  
					}


				}



				if ( $rate ) {      
					$price    = round( ( $price * $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);         
				}
			}

			return $price;
		}

		public function wccs_custom_variation_price( $price, $variation, $product ) {

			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}
				
				$rate = $this->wccs_get_currency_rate();
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;

				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {
					$individual_rate = trim(get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ));
					if ( '' != $individual_rate && 0 != $individual_rate ) {
						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                            
						return $price;                  
					}
				}

				if ( $rate ) {                  
					$price    = round( ( $price * $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);
				}
			}

			return $price;
		}

		public function wccs_subscription_product_price( $price, $product ) {           

			// wp_die($price);
			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}
				
				$rate = $this->wccs_get_currency_rate();
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;
				//print_r( $current_currency_info );

				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {
					$individual_rate = trim(get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ));
					if ( '' != $individual_rate && 0 != $individual_rate ) {
						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                            
						return $price;                  
					}
				}

				if ( $rate ) {                  
					$price    = round( ( $price * $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);
				}
			}
			return $price;
		}

		public function wccs_subscription_product_price_signup( $price, $product ) {            

			// wp_die($price);
			if ( ( ! is_admin() && is_numeric( $price ) ) || ( wp_doing_ajax() && is_numeric( $price ) ) ) {

				if ( 'stop' == $this->is_early_renew_subscription() ) {
					return $price;
				}
				
				$rate = $this->wccs_get_currency_rate();
				$decimals = $this->wccs_get_currency_decimals();
				$current_currency_info = $this->currency_info;
				//print_r( $current_currency_info );

				if ( isset( $current_currency_info['zone_id'] ) && 0 <= $current_currency_info['zone_id'] ) {
					$individual_rate = trim(get_post_meta( $product->get_id(), 'wccs_zp_override_rate_for_' . $current_currency_info['zone_id'], true ));
					if ( '' != $individual_rate && 0 != $individual_rate ) {
						$rate = $individual_rate;
						$price = round( ( $price * $rate ), $decimals );                            
						return $price;                  
					}
				}

				if ( $rate ) {                  
					$price    = round( ( $price * $rate ), $decimals );
					$price = $this->wccs_get_currency_rounding($price);
					$price = $this->wccs_get_currency_charming($price);
				}
			}
			return $price;
		}

		public function get_formatted_product_subtotal( $product_subtotal, $product, $quantity, $cart ) {

			$signup_fee = get_post_meta( $product->get_id(), '_subscription_sign_up_fee', true );

			return $product_subtotal;
		}

		public function wccs_add_user_to_variation_prices_hash( $hash ) {
			if ( ! is_admin() || wp_doing_ajax() ) {
				if ( get_current_user_id() ) {
					if ( $this->wccs_get_currency() ) {
						$hash[] = get_current_user_id() . '-' . $this->currency;
					} else {
						$hash[] = get_current_user_id();
					}
				} elseif ( $this->wccs_get_currency() ) {
						$hash[] = WC_Geolocation::get_ip_address() . '-' . $this->currency;
				} else {
					$hash[] = WC_Geolocation::get_ip_address();
				}
			}

			return $hash;
		}

		public function wccs_detect_currency() {            

			global $woocommerce;
			if ( ! empty( $woocommerce->cart->get_cart() ) ) {
				foreach ( $woocommerce->cart->get_cart() as $key => $value ) {
					if ( isset( $value['subscription_renewal'] ) && isset( $value['subscription_renewal']['subscription_renewal_early'] ) ) {   
						if ( $value['subscription_renewal']['subscription_renewal_early'] ) {
							return false;                           
						}
					}
				}
			}           


			if ( '1' == get_option( 'wccs_zp_toggle' ) && '1' == get_option( 'wccs_currency_by_location' ) && '1' == get_option( 'wccs_pay_by_user_currency' ) ) {              
				/**
				 * Filter
				 * 
				 * @since 1.0.0
				 */
				do_action( 'wccs_zone_pricing' );
				return false;           
			}           

			if ( get_option( 'wccs_currency_by_lang' ) ) {
				/**
				 * Filter
				 * 
				 * @since 1.0.0
				 */
				do_action( 'wccs_detect_wpml_lang' );
				return false;
			}

			$detect_currency = $this->storage->get_val( 'wccs_current_currency' );
			$data = wccs_get_client_ip_server();

			// echo '<pre>$data$data';
			// print_r($data);
			// echo '</pre>';

			if ( $data['geoplugin_currencyCode'] != $this->storage->get_val( 'set_by_location_currency' ) && empty( $detect_currency ) ) {
				//echo 'currency is changed';       
				// 'currency is change';
				$this->storage->remove_val( 'set_by_location_currency' );
			}

			if ( '1' == get_option( 'wccs_currency_by_location' ) && empty( $this->storage->get_val( 'set_by_location_currency' ) ) ) {
				$detect_currency = '';
				// remove storage
				$this->storage->remove_val( 'wccs_current_currency' );
			}

			if ( ( isset( $_REQUEST['wcc_switcher'] ) && sanitize_text_field( $_REQUEST['wcc_switcher'] ) ) || ( isset( $_GET['currency'] ) && sanitize_text_field( $_GET['currency'] ) ) ) {

				$currencies = $this->wccs_get_currencies();

				if ( isset( $_REQUEST['wcc_switcher'] ) && isset( $currencies[ sanitize_text_field( $_REQUEST['wcc_switcher'] ) ] ) ) {

					$this->currency      = sanitize_text_field( $_REQUEST['wcc_switcher'] );
					$this->currency_info = $currencies[ sanitize_text_field( $_REQUEST['wcc_switcher'] ) ];

					// set storage
					$this->storage->set_val( 'wccs_current_currency', $this->currency );
					$this->storage->set_val( 'set_by_location_currency', $this->currency );

				} elseif ( isset( $_GET['currency'] ) && isset( $currencies[ sanitize_text_field( $_GET['currency'] ) ] ) ) {

					$this->currency      = sanitize_text_field( $_GET['currency'] );
					$this->currency_info = $currencies[ sanitize_text_field( $_GET['currency'] ) ];

					// set storage
					$this->storage->set_val( 'wccs_current_currency', $this->currency );
					$this->storage->set_val( 'set_by_location_currency', $this->currency );

				} else {

					$this->currency      = null;
					$this->currency_info = array();

					// remove storage
					$this->storage->remove_val( 'wccs_current_currency' );

				}
			} elseif ( empty( $detect_currency ) && '1' == get_option( 'wccs_currency_by_location' ) && empty( $this->storage->get_val( 'set_by_location_currency' ) ) ) { // && !isset( $_SESSION['set_by_location'] )

				$currencies = $this->wccs_get_currencies();

				if ( isset( $data['geoplugin_currencyCode'] ) && ! empty( sanitize_text_field( $data['geoplugin_currencyCode'] ) ) && isset( $currencies[ sanitize_text_field( $data['geoplugin_currencyCode'] ) ] ) ) {

					$this->currency      = sanitize_text_field( $data['geoplugin_currencyCode'] );
					$this->currency_info = $currencies[ sanitize_text_field( $data['geoplugin_currencyCode'] ) ];

					// set storage
					$this->storage->set_val( 'wccs_current_currency', $this->currency );
					$this->storage->set_val( 'set_by_location_currency', $this->currency );

				} else {
					$this->currency      = null;
					$this->currency_info = array();

					// remove storage
					$this->storage->remove_val( 'wccs_current_currency' );
				}
			}

			WC()->cart->calculate_totals();
		}

		public function wccs_get_currencies() {
			$currencies = array();

			if ( '1' == get_option( 'wccs_zp_toggle' ) && '1' == get_option( 'wccs_currency_by_location' ) && '1' == get_option( 'wccs_pay_by_user_currency' ) ) {  

				$wccs_zp_data = get_option( 'wccs_zp_data', false );                            
				if ( false != $wccs_zp_data && count( $wccs_zp_data ) > 0 ) {                   

					foreach ( $wccs_zp_data as $key => $zp_data ) {
						$currencies[ $zp_data['zp_currency'] ] = array(
							'zone_id' => $key,
							'rate' => $zp_data['zp_rate'],
							// 'symbol' => get_woocommerce_currency_symbol( $zp_data['zp_currency'] ),
							'decimals' => isset($zp_data['zp_decimal']) ? $zp_data['zp_decimal'] : 0,
						);
					}
					
					return $currencies;
				}
			}

			if ( $this->currencies ) {
				// echo '<pre>$this->currencies$this->currencies in 2';
				// print_r( $this->currencies );
				// echo '</pre>';
				$currencies = $this->currencies;
			}

			return $currencies;
		}

		public function wccs_get_default_currency() {
			$default = null;
			if ( $this->default_currency ) {
				$default = $this->default_currency;
			}
			return $default;
		}

		public function wccs_get_default_currency_flag() {
			$default = null;
			if ( $this->default_currency_flag ) {
				$default = $this->default_currency_flag;
			}
			return $default;
		}

		public function wccs_get_currency() {
			$currency = null;
			//echo( 'test 2 ' . $this->currency . '<br>' );
			if ( $this->currency ) {
				$currency = $this->currency;
			}
			return $currency;
		}

		public function wccs_get_currency_symbol() {
			$symbol = null;
			$prefix = null;
			$info   = $this->currency_info;
			if ( isset( $info['symbol'] ) ) {
				if ( isset( $info['symbol_prefix'] ) ) {
					$prefix = $info['symbol_prefix'];
				}
				$symbol = $prefix . $info['symbol'];
			}
			return $symbol;
		}

		public function wccs_get_currency_format() {
			$format = null;
			$info   = $this->currency_info;
			if ( isset( $info['format'] ) ) {
				$format = $info['format'];
			}
			return $format;
		}

		public function wccs_get_currency_rate() {
			
			$rate = null;
			$info = $this->currency_info;

			if ( isset( $info['rate'] ) ) {
				$rate = $info['rate'];
			}
			return $rate;
		}

		public function wccs_get_currency_rounding( $price ) {
			
			$rounding = 0;
			$info = $this->currency_info;

			if ( isset( $info['rounding'] ) ) {
				$rounding = $info['rounding'];
			}

			$price_break = explode( '.', $price);

			//Price Rounding
			if ( isset($price_break[1]) && '' != $price_break[1] ) {

				$decimal_num = (int) $price_break[1];

				if ( $decimal_num > 0 ) {
					if ( '0.25' == $rounding ) {
						$rounding = (float) $rounding;
						$num = $this->closestNumber($price, $rounding);
						$added_price = $num - $price;
						$price = $price + $added_price;
					}

					if ( '0.5' == $rounding ) {
						$rounding = (float) $rounding;
						$num = $this->closestNumber($price, $rounding);
						$added_price = $num - $price;
						$price = $price + $added_price;
					}

					if ( '1' == $rounding ) {
						$rounding = (int) $rounding;
						$num = $this->closestNumber($price, $rounding);
						$added_price = $num - $price;
						$price = $price + $added_price;
					}

					if ( '5' == $rounding ) {
						$rounding = (int) $rounding;
						$num = $this->closestNumber($price, $rounding);
						$added_price = $num - $price;
						$price = $price + $added_price; 
					}

					if ( '10' == $rounding ) {
						$rounding = (int) $rounding;
						$num = $this->closestNumber($price, $rounding);
						$added_price = $num - $price;
						$price = $price + $added_price;
					}
				}                       
			}

			return $price;
		}

		public function wccs_get_currency_charming( $price ) {
			
			$charming = 0;
			$info = $this->currency_info;

			if ( isset( $info['charming'] ) ) {
				$charming = $info['charming'];
			}
			
			//Price Charming
			if ( '-0.01' == $charming ) {
				$charming = (float) $charming;
				$price = $price + $charming;
			}

			if ( '-0.05' == $charming ) {
				$charming = (float) $charming;
				$price = $price + $charming;
			}

			if ( '-0.10' == $charming ) {
				$charming = (float) $charming;
				$price = $price + $charming;
			}

			return $price;
		}

		public function wccs_get_currency_decimals() {
			$decimals = wc_get_price_decimals();
			$info     = $this->currency_info;
			if ( isset( $info['decimals'] ) ) {
				$decimals = $info['decimals'];
			}
			return $decimals;
		}

		public function wcc_switcher_shortcode_callback( $atts ) {

			if ( '1' != get_option( 'wccs_zp_toggle' ) ) {
				ob_start();

				$args = shortcode_atts(
					array(
						'class' => '',
						'style' => '',
					),
					$atts
				);

				if ( $args['style'] && in_array( $args['style'], array( 'style_01', 'style_02', 'style_03', 'style_04' ) ) ) {
					$style = $args['style'];
				} else {
					$style = get_option( 'wccs_shortcode_style', 'style_01' );
				}

				$variables                          = array();
				$variables['class']                 = $args['class'];
				$variables['default_currency']      = $this->wccs_get_default_currency();
				$variables['default_currency_flag'] = $this->wccs_get_default_currency_flag();
				$variables['default_label']         = wccs_get_currency_label( $variables['default_currency'] );
				$variables['default_symbol']        = get_woocommerce_currency_symbol( $variables['default_currency'] );
				$variables['currencies']            = $this->wccs_get_currencies();
				$variables['currency']              = $this->wccs_get_currency();
				$variables['show_currency']         = get_option( 'wccs_show_currency', 1 );
				$variables['show_flag']             = get_option( 'wccs_show_flag', 1 );

				$this->render_template( WCCS_PLUGIN_PATH . 'templates/' . $style . '.php', $variables );

				return ob_get_clean();
			}
		}

		public function wcc_rates_shortcode_callback( $atts ) {

			if ( '1' != get_option( 'wccs_zp_toggle' ) ) {
				ob_start();

				$args = shortcode_atts(
					array(
						'class' => '',
					),
					$atts
				);

				$variables                          = array();
				$variables['class']                 = $args['class'];
				$variables['default_currency']      = $this->wccs_get_default_currency();
				$variables['default_currency_flag'] = $this->wccs_get_default_currency_flag();
				$variables['default_label']         = wccs_get_currency_label( $variables['default_currency'] );
				$variables['default_symbol']        = get_woocommerce_currency_symbol( $variables['default_currency'] );
				$variables['currencies']            = $this->wccs_get_currencies();
				$variables['currency']              = $this->wccs_get_currency();
				$variables['show_currency']         = get_option( 'wccs_show_currency', 1 );
				$variables['show_flag']             = get_option( 'wccs_show_flag', 1 );

				$this->render_template( WCCS_PLUGIN_PATH . 'templates/rates.php', $variables );

				return ob_get_clean();
			}
		}

		// public function wccs_test_shortcode() {
		// echo do_shortcode('[wcc_switcher]');
		// }

		public function wccs_widgets() {
			register_widget( 'WCC_Switcher' );
		}

		public function wccs_get_nav_menu_items_filter( $items, $menu, $args ) {

			// if ( is_account_page() ) { //donot show on account page of user
			//  return $items;
			// }

			if ( get_option( 'wccs_show_in_menu', 0 ) && ! is_admin() && '1' != get_option( 'wccs_zp_toggle' ) ) {
				$toAdd            = array();
				$target_menu      = get_option( 'wccs_switcher_menu', '' );
				$show_flag        = get_option( 'wccs_show_flag', 1 );
				$show_currency    = get_option( 'wccs_show_currency', 1 );
				$currencies       = $this->wccs_get_currencies();
				$currency         = $this->wccs_get_currency();
				$default_currency = $this->wccs_get_default_currency();
				$default_label    = wccs_get_currency_label( $default_currency );
				$default_symbol   = get_woocommerce_currency_symbol( $default_currency );
				$counter          = count( $items ) + 1;
				$title            = '';

				if ( count( $currencies ) && $menu->slug == $target_menu ) {
					$item = array();

					$item['ID']                    = 'wcss_' . $default_currency;
					$item['db_id']                 = 'wcss_' . $default_currency;
					$item['object_id']             = 'wcss_' . $default_currency;
					$item['default_currency_flag'] = $this->wccs_get_default_currency_flag();
					$item['object']                = 'wcss_menu_item';
					$item['type']                  = 'wcss_menu_item';
					$item['menu_order']            = $counter;
					$item['target']                = '';
					$item['xfn']                   = '';
					$item['wcc_id']                = 'wcss_' . $default_currency;
					if ( $currency ) {
						$item['menu_item_parent'] = 'wcss_' . $currency;
					} else {
						$item['menu_item_parent'] = '';
					}
					$item['classes']   = array( 'menu-item', 'wccs-click-for-menu', 'wccs-menu-item', 'wccs-menu-item-' . $default_currency );
					$item['post_type'] = 'nav_menu_item';
					$title             = $default_label;
					if ( $show_currency ) {
						$title .= ' "' . $default_symbol . '" ';
					}
					// $title .= esc_html__('(Default)', 'wccs');
					if ( $show_flag ) {
						$title .= ' <span class="wcc-flag flag-icon flag-icon-' . $item['default_currency_flag'] . '"></span>';
					}
					$item['title'] = $title;
					$item['url']   = '#' . $default_currency;

					$toAdd[] = (object) $item;

					foreach ( $currencies as $code => $info ) {
						$counter++;
						$item = array();

						$item['ID']         = 'wcss_' . $code;
						$item['db_id']      = 'wcss_' . $code;
						$item['object_id']  = 'wcss_' . $code;
						$item['object']     = 'wcss_menu_item';
						$item['type']       = 'wcss_menu_item';
						$item['menu_order'] = $counter;
						$item['target']     = '';
						$item['xfn']        = '';
						$item['wcc_id']     = 'wcss_' . $default_currency;
						if ( $currency ) {
							if ( $currency != $code ) {
								$item['menu_item_parent'] = 'wcss_' . $currency;
							} else {
								$item['menu_item_parent'] = '';
							}
						} else {
							$item['menu_item_parent'] = 'wcss_' . $default_currency;
						}
						$item['classes']   = array( 'menu-item', 'wccs-click-for-menu', 'wccs-menu-item', 'wccs-menu-item-' . $code );
						$item['post_type'] = 'nav_menu_item';
						/**
						 * Filter
						 * 
						 * @since 1.0.0
						 */
						$title             = apply_filters('wccs_change_default_currency_label', $info['label']);
						if ( $show_currency ) {
							$title .= ' "' . $info['symbol'] . '"';
						}

						if ( $show_flag ) {
							$title .= ' <span class="wcc-flag flag-icon flag-icon-' . $info['flag'] . '"></span>';
						}
						$item['title'] = $title;
						$item['url']   = '#' . $code;

						$toAdd[] = (object) $item;
					}

					return array_merge( $items, $toAdd );
				}
			}

			return $items;
		}

		public function render_template( $template_path, $data = array() ) {
			extract( $data );
			if ( file_exists( $template_path ) ) {
				require $template_path;
			} else {
				/* translators: %s template */
				wc_doing_it_wrong(__FUNCTION__, sprintf(__(' %s does not exist.', 'wccs'), '<code>' . $template_path . '</code>'), '2.1');
				return;
			}
		}

		public function wccs_add_sticky_callback() {
			/**
			* Filter wccs_before_sticky_swticher
			* 
			* @since 1.0
			**/
			if ( ! apply_filters('wccs_before_sticky_swticher', true) ) {
				return false;
			}

			if ( get_option( 'wccs_sticky_switcher', 0 ) && '1' != get_option( 'wccs_zp_toggle' ) ) {
				$default_currency = $this->wccs_get_default_currency();
				$default_label    = wccs_get_currency_label( $default_currency );
				// $default_symbol = get_woocommerce_currency_symbol($default_currency);
				$currencies = $this->wccs_get_currencies();
				$currency   = $this->wccs_get_currency();
				// $show_currency = get_option('wccs_show_currency', 1);
				$show_flag = get_option( 'wccs_show_flag', 1 );

				if ( count( $currencies ) ) {
					wp_enqueue_style( 'wccs_flags_style', WCCS_PLUGIN_URL . 'assets/lib/flag-icon/flag-icon.css', '', '1.0' );
					wp_enqueue_style( 'wccs_slick_css', WCCS_PLUGIN_URL . 'assets/frontend/css/wccs_slick.css', '', '1.0' );
					wp_enqueue_style( 'wccs_sticky_css', WCCS_PLUGIN_URL . 'assets/frontend/themes/sticky/theme-05.css', '', '1.0&t=' . gmdate('dmYHis') );

					wp_enqueue_script( 'wccs_slick_script', WCCS_PLUGIN_URL . 'assets/frontend/js/wccs_slick.min.js', array( 'jquery' ), '1.0' );
					
					wp_enqueue_script( 'wccs_sticky_script', WCCS_PLUGIN_URL . 'assets/frontend/themes/sticky/sticky.js', array( 'jquery' ), '1.0&t=' . gmdate('dmYHis') );
					?>
					<div id="wcc-sticky-list-wrapper" class="<?php if ( count( $currencies ) > 4 ) { ?>
					wcc-with-more<?php } ?> 
					<?php if ( get_option( 'wccs_sticky_position', 'right' ) == 'left' ) { ?>
					wcc-sticky-left<?php } ?>">
						<div id="wccs_sticky_container" class="noMoreTop">
							<a href="#" id="wccs_sticky_up"></a>
							<ul class="wcc-sticky-list">
								<li class="d-flex sticky-def <?php if ( ! $currency ) { ?>
								crnt<?php } ?>" data-code="<?php echo esc_attr( $default_currency ); ?>">
									<span class="wcc-name"><?php echo esc_html( $default_currency ); ?></span>
									<?php if ( ! empty( $this->wccs_get_default_currency_flag() ) ) : ?>
										<span class="wcc-flag <?php if ( $show_flag && $this->wccs_get_default_currency_flag() ) { ?>
										flag-icon flag-icon-<?php echo esc_attr( $this->wccs_get_default_currency_flag() ); } ?>"></span>
									<?php else : ?>
										<span class="wcc-flag"><?php echo esc_html__( 'Def', 'wccs' ); ?></span>
									<?php endif; ?>
								</li>
								<?php
								foreach ( $currencies as $code => $info ) {
									?>
								<li class="d-flex <?php if ( $code == $currency ) { ?>
								crnt<?php } ?>" data-code="<?php echo esc_attr( $code ); ?>">
									<span class="wcc-name"><?php echo esc_html( $code ); ?></span>
									<span class="wcc-flag <?php if ( $show_flag && $info['flag'] ) { ?>
									flag-icon flag-icon-<?php echo esc_attr( $info['flag'] ); } ?>"></span>
								</li>
									<?php
								}
								?>
							</ul>
							<a href="#" id="wccs_sticky_down"></a>
						</div>
					</div>
					<form class="wccs_sticky_form" method="post" action="" style="display: none;">
						<?php wp_nonce_field( '_wccsnonce', '_wccsnonce' ); ?>
						<input type="hidden" name="wcc_switcher" class="wcc_switcher" value="">
					</form>
					<?php
				}
			}
		}

		private function closestNumber( $n, $m ) {  
			// find the quotient  
			$q = (int) ( $n / $m );  
			  
			// 1st possible closest number  
			//$n1 = $m * $q;  
			  
			// 2nd possible closest number  
			$n2 = ( $n * $m ) > 0 ? ( $m * ( $q + 1 ) ) : ( $m * ( $q - 1 ) );  
			  
			// if true, then n1 is the  
			// required closest number  
			//if (abs($n - $n1) < abs($n - $n2))  
				//return $n1;  
			  
			// else n2 is the required  
			// closest number  
			return $n2;  
		}
	}

	$wccs            = new WCCS();
	$GLOBALS['WCCS'] = $wccs;
}
