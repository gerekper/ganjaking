<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_YWF_Deposit_Fund_Checkout' ) ) {

	class YITH_YWF_Deposit_Fund_Checkout {

		protected static $instance;

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'add_deposit_to_cart' ), 20 );


			add_filter( 'woocommerce_add_cart_item', array( $this, 'set_price_deposit' ), 20, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'set_price_deposit_from_session'
			), 20, 3 );

			add_action( 'woocommerce_restore_cart_item', array( $this, 'set_price_restore_cart_item' ), 20, 2 );

			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'valid_add_to_cart' ), 20, 5 );

			add_filter( 'woocommerce_coupons_enabled', array( $this, 'disable_coupons_for_deposit' ), 99, 1 );

			add_action( 'woocommerce_remove_cart_item', array( $this, 'clear_deposit_session' ), 20, 2 );

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), 20 );
			//check user profile
			add_action( 'before_make_a_deposit_form', array( $this, 'display_available_user_funds' ), 10 );
			add_action( 'woocommerce_customer_save_address', array( $this, 'redirect_to_make_a_deposit' ) );

			add_filter( 'yith_payouts_register_new_payout', array( $this, 'exclude_funds_deposits' ), 99, 2 );

		}

		/**
		 * @return YITH_YWF_Deposit_Fund_Checkout unique access
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * check if amount is right and set the session
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function validate_amount() {
			$amount = wc_format_decimal( $_REQUEST['amount_deposit'] );

			if ( '' === $amount || ! is_numeric( $amount ) ) {
				wc_add_notice( __( 'Enter a price', 'yith-woocommerce-account-funds' ), 'error' );

				return false;
			}

			$amount = floatval( $amount );
			$min    = floatval( wc_format_decimal( ywf_get_min_fund_rechargeable() ) );
			$max    = ywf_get_max_fund_rechargeable();


			if ( $amount < $min ) {
				wc_add_notice( sprintf( '%s %s', __( 'Minimum deposit amount is', 'yith-woocommerce-account-funds' ), wc_price( $min ) ), 'error' );

				return false;
			}

			if ( $max != '' ) {
				$max = floatval( wc_format_decimal( $max ) );

				if ( $amount > $max ) {
					wc_add_notice( sprintf( '%s %s', __( 'Maximum deposit amount is', 'yith-woocommerce-account-funds' ), wc_price( $max ) ), 'error' );

					return false;
				}
			}

			return $amount;
		}


		/**
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function display_available_user_funds() {

			echo do_shortcode('[yith_ywf_show_user_fund]');
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function redirect_to_make_a_deposit() {

			$make_deposit_endpoint = apply_filters( 'ywf_make_deposit_slug', 'make-a-deposit' );
			if ( isset( $_GET['return_to'] ) && $make_deposit_endpoint === $_GET['return_to'] ) {

				$url          = wc_get_page_permalink( 'myaccount' );
				$endpoint_url = esc_url( wc_get_endpoint_url( $make_deposit_endpoint, '', $url ) );

				wp_redirect( $endpoint_url );
				exit;
			}
		}


		/**set gateways
		 *
		 * @param $gateways
		 *
		 * @return mixed
		 * @author YIThemes
		 * @since 1.0.8
		 *
		 */
		public function available_payment_gateways( $gateways ) {

			if ( isset( WC()->session ) && WC()->session->get( 'deposit_amount', false ) ) {

				$deposit_payments = get_option( 'ywf_select_gateway' );
				unset( $gateways['yith_funds'] );
				if ( ! empty( $deposit_payments ) ) {

					foreach ( $gateways as $key => $gateway ) {
						if ( ! in_array( $key, $deposit_payments ) ) {
							unset( $gateways[ $key ] );
						}
					}
				}
			}

			return $gateways;
		}

		/**
		 * @throws Exception
		 */
		public function add_deposit_to_cart() {

			if ( isset( $_POST['deposit_nonce'] ) && wp_verify_nonce( $_POST['deposit_nonce'], 'add_deposit' ) ) {

				$product_id = get_option( '_ywf_deposit_id' );

				WC()->cart->empty_cart( true );

				$amount = $this->validate_amount();


				if ( ! $amount ) {

					wp_safe_redirect( remove_query_arg( array( 'amount_deposit', 'deposit_nonce' ) ) );
					exit;
				}

				$cart_item_data = apply_filters( 'yith_account_funds_deposit_item_data', array( 'amount_deposit' => $amount ) );
				WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );

				WC()->session->set( 'deposit_amount', $amount );
				wp_safe_redirect( wc_get_page_permalink( 'checkout' ) );
				exit;
			}
		}

		/**
		 * @param bool $is_valid
		 * @param $product_id
		 * @param $quantity
		 * @param string $variation_id
		 * @param string $variations
		 *
		 * @return bool
		 */
		public function valid_add_to_cart( $is_valid, $product_id, $quantity, $variation_id = '', $variations = '' ) {

			if ( $this->is_deposit_in_cart() ) {

				$product       = wc_get_product( $product_id );
				$error_message = sprintf( __( 'You cannot add &quot;%s&quot; to the cart because you are depositing funds', 'yith-woocommerce-account-funds' ), $product->get_name() );
				wc_add_notice( $error_message, 'error' );
				$is_valid = false;
			}

			return $is_valid;
		}

		/**
		 * check if the deposit product is in the cart
		 * @return bool
		 * @since 1.1.0
		 * @author Salvatore Strano
		 */
		public function is_deposit_in_cart() {

			$is_in_cart = false;
			if ( isset( WC()->cart ) && ! WC()->cart->is_empty() ) {

				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

					$product = $cart_item['data'];

					if ( $product->get_type() == 'ywf_deposit' ) {
						return true;
					}
				}
			}

			return $is_in_cart;
		}

		/**
		 * @param array $cart_item_data
		 * @param string $cart_item_key
		 *
		 * @return array
		 */
		public function set_price_deposit( $cart_item_data, $cart_item_key ) {

			if ( isset( $cart_item_data['amount_deposit'] ) ) {


				$deposit_amount = apply_filters( 'yith_fund_deposit_amount_for_session', $cart_item_data['amount_deposit'], $cart_item_data );
				$cart_item_data['data']->set_price( $deposit_amount );
			}

			return $cart_item_data;
		}

		/**
		 * @param array $session_data
		 * @param array $values
		 * @param string $key
		 *
		 * @return array
		 */
		public function set_price_deposit_from_session( $session_data, $values, $key ) {

			if ( isset( $session_data['amount_deposit'] ) ) {

				$deposit_amount = apply_filters( 'yith_fund_deposit_amount_for_session', $session_data['amount_deposit'], $session_data );

				$session_data['data']->set_price( $deposit_amount );
			}

			return $session_data;
		}

		/**
		 * @param string $cart_item_key
		 * @param WC_Cart $cart
		 */
		public function set_price_restore_cart_item( $cart_item_key, $cart ) {


			if ( isset( $cart->cart_contents[ $cart_item_key ]['amount_deposit'] ) ) {
				$amount   = $cart->cart_contents[ $cart_item_key ]['amount_deposit'];
				$currency = get_woocommerce_currency();
				$amount   = apply_filters( 'yith_fund_deposit_amount', $amount, $currency );
				$cart->cart_contents[ $cart_item_key ]['data']->set_price( $amount );

			}
		}

		/**
		 * @param string $cart_item_key
		 * @param WC_Cart $cart
		 */
		public function clear_deposit_session( $cart_item_key, $cart ) {

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			/**
			 * @var WC_Product $product
			 */
			$product = $cart_item['data'];


			if ( $product->get_type() == 'ywf_deposit' ) {

				WC()->session->set( 'deposit_amount', false );
			}
		}


		/**
		 * @param bool $is_enabled
		 *
		 * @return bool
		 */
		public function disable_coupons_for_deposit( $is_enabled ) {

			if ( isset( WC()->session ) && WC()->session->get( 'deposit_amount', false ) ) {


				$is_enabled = get_option( 'yith_funds_enable_coupon', 'no' ) == 'yes' ? true : false;

			}

			return $is_enabled;
		}

		/**
		 * @param bool $register
		 * @param int $order_id
		 */
		public function exclude_funds_deposits( $register, $order_id ) {

			$order = wc_get_order( $order_id );

			if ( $order->get_item_count() == 1 ) {
				/**
				 * @var WC_Order_Item $item
				 */
				foreach ( $order->get_items() as $item ) {
					$product_id = wc_get_order_item_meta( $item->get_id(), '_product_id', true );

					$product = wc_get_product( $product_id );

					if ( $product->is_type( 'ywf_deposit' ) ) {

						$register = false;
						break;
					}
				}
			}

			return $register;
		}


	}
}
/**
 * @return YITH_YWF_Deposit_Fund_Checkout
 */
function YITH_YWF_Deposit_Fund_Checkout() {

	return YITH_YWF_Deposit_Fund_Checkout::get_instance();
}
