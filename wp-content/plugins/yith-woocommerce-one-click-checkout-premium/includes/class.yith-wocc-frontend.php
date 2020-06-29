<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce One-Click Checkout Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WOCC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WOCC_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WOCC_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WOCC_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WOCC_VERSION;

		/**
		 * Current user id
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_user_id = '';

		/**
		 * Action create order
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_order_action = 'yith_wocc_create_order';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WOCC_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->_user_id = get_current_user_id();

			// enqueue style and scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

			add_action( 'woocommerce_before_single_product', array( $this, 'add_button' ) );

			if( isset( $_REQUEST['_yith_wocc_one_click'] ) && $_REQUEST['_yith_wocc_one_click'] == 'is_one_click' ) {

				add_action( 'wp_loaded', array( $this, 'empty_cart' ), 1 );

				// filter redirect url after add to cart
				add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'one_click_url' ), 99, 1 );
			}

			// main action
			add_action( 'wp_loaded', array( $this, 'one_click_handler' ), 99 );

		}

		/**
		 * Enqueue scripts
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {
			
			$min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'yith-wocc-script', YITH_WOCC_ASSETS_URL . '/js/yith-wocc-frontend'.$min.'.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'yith-wocc-style', YITH_WOCC_ASSETS_URL . '/css/yith-wocc-frontend.css', array(), $this->version, 'all' );

			// custom style
			$custom_css = yith_wocc_get_custom_style();
			wp_add_inline_style( 'yith-wocc-style', $custom_css );
		}

		/**
		 * Add one click button
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_button() {
            /**
             * @type $product WC_Product
             */
			global $product;

			if( $product->is_type( 'external' ) || ! $this->customer_can() ) {
				return;
			}

			if( $product->is_type( 'variable' ) ) {
				add_action( 'woocommerce_after_single_variation', array( $this, 'print_button' ) );
			}
			else {
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'print_button' ) );
			}
		}

		/**
		 * Add one click button
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $custom_args
		 * @author Francesco Licandro
		 */
		public function print_button( $custom_args = array() ) {

			global $product;

			$args = array(
				'label'         => get_option( 'yith-wocc-button-label', '' ),
				'divider'       => get_option( 'yith-wocc-show-form-divider' ) == 'yes',
				'product'       => ''
			);

			// merge with custom args
			if( ! empty( $custom_args ) && is_array( $custom_args ) ) {
				$args = array_merge( $args, $custom_args );
			}

			// get global if no product set
			! $args['product'] && $args['product'] = $product;

			// if product empty return
			if( ! $args['product'] || is_null( $args['product'] ) ) {
				return;
			}

			// let filter template args
			$args = apply_filters( 'yith_wocc_template_args', $args );

			wc_get_template( 'yith-wocc-form.php', $args, '', YITH_WOCC_DIR . 'templates/' );
		}

		/**
		 * Empty current cart and store it in variables
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function empty_cart() {
		    // save current cart
			$cart = WC()->session->get( 'cart' );
			$this->_user_id && update_user_meta( $this->_user_id, '__yith_wocc_persistent_cart', $cart );

			WC()->cart->empty_cart( true );
		}

		/**
		 * @param $url
		 * @return string
		 * @author Francesco Licandro
		 */
		public function one_click_url( $url ) {

			if( ! isset( $_REQUEST['add-to-cart'] ) ) {
                return $url;
            }

			$product_id = intval( $_REQUEST['add-to-cart'] );

			$args   = apply_filters( 'yith_wocc_one_click_url_args', array(
                '_ywocc_action'     => $this->_order_action,
                '_ywocc_nonce'      => wp_create_nonce( $this->_order_action ),
                '_ywocc_product'    => $product_id
            ) );
			$url    = esc_url_raw( add_query_arg( $args, get_permalink( $product_id ) ) );

			return apply_filters( 'yith_wocc_one_click_url', $url, $product_id );
		}

		/**
		 * One click handler
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function one_click_handler() {

			// check action and relative nonce
			if( is_admin()
				|| ! isset( $_GET['_ywocc_action'] ) || $_GET['_ywocc_action'] != $this->_order_action
			    || ! isset( $_GET['_ywocc_nonce'] ) || ! wp_verify_nonce( $_GET['_ywocc_nonce'], $this->_order_action )
				|| ! $this->customer_can() ){

				return;
			}

			global $wpdb;
			$order = false;
			$url = isset( $_GET['_ywocc_product'] ) ? $this->get_product_url( intval( $_GET['_ywocc_product'] ) ) : '';

			wc_clear_notices(); // clear all old notice

			// unset chosen shipping method to get the default one
			WC()->session->__unset( 'chosen_shipping_methods' );
			// Ensure shipping methods are loaded early
			WC()->shipping();

			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				define( 'WOOCOMMERCE_CHECKOUT', true );
			}
			// prevent time limit
			wc_set_time_limit( 0 );

			try{

				// Start transaction if available
				$wpdb->query( 'START TRANSACTION' );

				// create new order
				$order = wc_create_order( array(
					'status'        => apply_filters( 'woocommerce_default_order_status', 'pending' ),
					'customer_id'   => $this->_user_id
				));

				if ( is_wp_error( $order ) ) {
					throw new Exception( sprintf( __( 'Error %d: Unable to create the order. Please try again.', 'yith-woocommerce-one-click-checkout' ), 400 ) );
				} else {
					$order_id = $order->get_id();
					do_action( 'woocommerce_new_order', $order_id );
				}

				// get billing/shipping user address
				$billing_address = apply_filters( 'yith_wocc_filter_billing_address', $this->get_user_billing_address( $this->_user_id ), $this->_user_id );
				if ( WC()->cart->needs_shipping() ) {
					$shipping_address = apply_filters( 'yith_wocc_filter_shipping_address', $this->get_user_shipping_address( $this->_user_id ), $this->_user_id );
					// if shipping address was empty set billing as shipping
					$shipping_address = empty( $shipping_address ) ? $billing_address : $shipping_address;

					$this->set_shipping_info( $shipping_address );
				}
				else {
					$shipping_address = array();
				}

				$data = array(
				    'billing'   => $billing_address,
                    'shipping'  => $shipping_address
                );

				// calculate totals
				WC()->cart->calculate_totals();

                $order = $this->process_order( $order, $order_id, $data );

                if ( is_wp_error( $order ) ) {
                    throw new Exception( $order->get_error_message() );
                }

                do_action( 'woocommerce_checkout_update_order_meta', $order_id, $data );

				// If we got here, the order was created without problems!
				$wpdb->query( 'COMMIT' );
			}
			catch ( Exception $e ) {
				// There was an error adding order data!
				$wpdb->query( 'ROLLBACK' );
				if ( $e->getMessage() ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}

				$order = false;
			}

			// action before redirection
			do_action( 'yith_wooc_handler_before_redirect', $order );

			if( $order ) {

				$message = __( 'Thank you. Your order has been received and it is now waiting for payment', 'yith-woocommerce-one-click-checkout' );

				if ( ! WC()->cart->needs_payment() ) {
					// No payment was required for order
					$order->payment_complete();
					// @new 1.0.5
					$message = __( 'Thank you. Your order has been received.', 'yith-woocommerce-one-click-checkout' );
				}

				$message = apply_filters( 'yith_wocc_success_msg_order_created', $message );
				wc_add_notice( $message, 'success' );
			}
			
			// restore persistent cart
			$this->restore_cart();
			// then redirect to product page ( prevent redirect to cart or other page )
            // if product url in not available get home
            ! $url && $url = home_url();
			wp_safe_redirect( apply_filters( 'yith_wocc_redirect_after_create_order', $url, $order ) );
			exit;
		}

        /**
         * Process order and add data before WooCommerce 3.0.0
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @param object $order \WC_Order
         * @param integer $order_id
         * @param array $data
         * @return WC_Order | WP_Error
         * @deprecated Use process_order instead
         */
        public function process_order_before_3( $order, $order_id, $data ){
            return $this->process_order( $order, $order_id, $data );
        }

        /**
         * Process order and add data after WooCommerce 3.0.0
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @param object $order WC_Order
         * @param integer $order_id
         * @param array $data
         * @return WC_Order|WP_Error
         */
        public function process_order( $order, $order_id, $data ){
            /**
             * @type $order \WC_Order
             */
            try {
                // Store address
                foreach ( $data as $address_key => $address ) {
                    foreach( $address as $key => $value ) {
                        // prepend key prefix
                        $key = $address_key . '_' . $key;
                        if (is_callable(array($order, "set_{$key}"))) {
                            $order->{"set_{$key}"}($value);
                            // Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
                        } elseif (0 === stripos($key, 'billing_') || 0 === stripos($key, 'shipping_')) {
                            $order->update_meta_data('_' . $key, $value);
                        }
                    }
                }

                $shipping_method = apply_filters( 'yith_wocc_filter_shipping_methods', WC()->session->get('chosen_shipping_methods') );

                if( WC()->cart->needs_shipping() ) {
                    if ( ! in_array( WC()->customer->get_shipping_country(), array_keys( WC()->countries->get_shipping_countries() ) ) || $this->check_shipping_available() ) {
                        throw new Exception(sprintf(__('Unfortunately <strong>we do not ship %s</strong>. Please enter an alternative shipping address.', 'yith-woocommerce-one-click-checkout'), WC()->countries->shipping_to_prefix() . ' ' . WC()->customer->get_shipping_country()));
                    }
                }

                $order->set_created_via( 'one-click' );
                $order->set_customer_id( apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() ) );
                $order->set_currency( get_woocommerce_currency() );
                $order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
                $order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
                $order->set_customer_user_agent( wc_get_user_agent() );
                $order->set_customer_note( '' );
                $order->set_payment_method( '' );
                $order->set_shipping_total( WC()->cart->shipping_total );
                $order->set_discount_total( WC()->cart->get_cart_discount_total() );
                $order->set_discount_tax( WC()->cart->get_cart_discount_tax_total() );
                $order->set_cart_tax( WC()->cart->tax_total );
                $order->set_shipping_tax( WC()->cart->shipping_tax_total );
                $order->set_total( WC()->cart->total );

                WC()->checkout->create_order_line_items( $order, WC()->cart );
                WC()->checkout->create_order_fee_lines( $order, WC()->cart );
                WC()->checkout->create_order_shipping_lines( $order, $shipping_method, WC()->shipping->get_packages() );
                WC()->checkout->create_order_tax_lines( $order, WC()->cart );

                /**
                 * Action hook to adjust order before save.
                 *
                 * @since 3.0.0
                 */
                do_action( 'woocommerce_checkout_create_order', $order, $data );

                // Save the order.
                $order->save();
            }
            catch ( Exception $e ) {
                // There was an error adding order data!
                return new WP_Error( 'one-click-error', $e->getMessage() );
            }

            return $order;
        }

        /**
         * Get current product url for one click action
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @param integer $id
         * @return string
         */
        public function get_product_url( $id ){
            $product = wc_get_product( $id );
            return $product ? $product->get_permalink() : '';
        }

        /**
         * Check if shipping is available for order
         *
         * @since 1.0.0
         * @author Francesco Licandro
         * @return boolean
         */
        public function check_shipping_available(){
            $packages = WC()->cart->get_shipping_packages();
            $no_shipping_available = false;
            foreach( $packages as $package ) {
                $shipping_zone = wc_get_shipping_zone( $package );
                // check for method
                $shipping_method = $shipping_zone->get_shipping_methods( true );
                if( ! empty( $shipping_method ) ) {
                    continue;
                }
                $no_shipping_available = true;
                break;
            }

            return $no_shipping_available;
        }

		/**
		 * Restore persistent cart
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function restore_cart(){

			// delete current cart
			WC()->cart->empty_cart( true );

			// update user meta with saved persistent
			$saved_cart = get_user_meta( $this->_user_id, '__yith_wocc_persistent_cart', true );
			// then reload cart
			WC()->session->set( 'cart', $saved_cart );
			WC()->cart->get_cart_from_session();
		}

		/**
		 * Get billing address for an user
		 *
		 * @since 1.0.0
		 * @param $id
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function get_user_billing_address( $id ) {

			// Formatted Addresses
			$billing = array(
				'first_name' => get_user_meta( $id, 'billing_first_name', true ),
				'last_name'  => get_user_meta( $id, 'billing_last_name', true ),
				'company'    => get_user_meta( $id, 'billing_company', true ),
				'address_1'  => get_user_meta( $id, 'billing_address_1', true ),
				'address_2'  => get_user_meta( $id, 'billing_address_2', true ),
				'city'       => get_user_meta( $id, 'billing_city', true ),
				'state'      => get_user_meta( $id, 'billing_state', true ),
				'postcode'   => get_user_meta( $id, 'billing_postcode', true ),
				'country'    => get_user_meta( $id, 'billing_country', true ),
				'email'      => get_user_meta( $id, 'billing_email', true ),
				'phone'      => get_user_meta( $id, 'billing_phone', true )
			);

			if ( ! empty( $billing['country'] ) ) {
				if( is_callable( array( WC()->customer, 'set_billing_country' ) ) ) {
                    WC()->customer->set_billing_country( $billing['country'] );
                }
                else {
                    WC()->customer->set_country( $billing['country'] );
                }
			}
			if ( ! empty( $billing['state'] ) ) {
                if( is_callable( array( WC()->customer, 'set_billing_state' ) ) ) {
                    WC()->customer->set_billing_state( $billing['state'] );
                }
                else {
                    WC()->customer->set_state( $billing['state'] );
                }
			}
			if ( ! empty( $billing['postcode'] ) ) {
                if( is_callable( array( WC()->customer, 'set_billing_postcode' ) ) ) {
                    WC()->customer->set_billing_postcode( $billing['postcode'] );
                }
                else {
                    WC()->customer->set_postcode( $billing['postcode'] );
                }
			}

			return apply_filters( 'yith_wocc_customer_billing', array_filter( $billing ) );
		}

		/**
		 * Get shipping address for an user
		 *
		 * @since 1.0.0
		 * @param $id
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function get_user_shipping_address( $id ) {

			if( ! WC()->cart->needs_shipping_address() ) {
				return array();
			}

			// Formatted Addresses
			$shipping = array(
				'first_name' => get_user_meta( $id, 'shipping_first_name', true ),
				'last_name'  => get_user_meta( $id, 'shipping_last_name', true ),
				'company'    => get_user_meta( $id, 'shipping_company', true ),
				'address_1'  => get_user_meta( $id, 'shipping_address_1', true ),
				'address_2'  => get_user_meta( $id, 'shipping_address_2', true ),
				'city'       => get_user_meta( $id, 'shipping_city', true ),
				'state'      => get_user_meta( $id, 'shipping_state', true ),
				'postcode'   => get_user_meta( $id, 'shipping_postcode', true ),
				'country'    => get_user_meta( $id, 'shipping_country', true )
			);

			return apply_filters( 'yith_wocc_customer_shipping', array_filter( $shipping ) );
		}

		/**
		 * Set shipping info for user
		 *
		 * @since 1.0.0
		 * @param mixed $values billing or shipping user info
		 * @author Francesco Licandro
		 */
		public function set_shipping_info( $values ) {

			// Update customer location to posted location so we can correctly check available shipping methods
			if ( ! empty( $values['country'] ) ) {
				WC()->customer->set_shipping_country( $values['country'] );
			}
			if ( ! empty( $values['state'] ) ) {
				WC()->customer->set_shipping_state( $values['state'] );
			}
			if ( ! empty( $values['postcode'] ) ) {
				WC()->customer->set_shipping_postcode( $values['postcode'] );
			}
		}

		/**
		 * Check if user can use one click feature
		 *
		 * @since 1.0.0
		 * @return boolean
		 * @author Francesco Licandro
		 */
		public function customer_can() {

			$return = true;

			if( ! is_user_logged_in() || ! get_user_meta( $this->_user_id, 'billing_email', true ) ) {
				$return = false;
			}

			return apply_filters( 'yith_wocc_customer_can', $return );
		}
	}
}
/**
 * Unique access to instance of YITH_WOCC_Frontend class
 *
 * @return \YITH_WOCC_Frontend
 * @since 1.0.0
 */
function YITH_WOCC_Frontend(){
	return YITH_WOCC_Frontend::get_instance();
}