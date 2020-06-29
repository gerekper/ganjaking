<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Subscription' ) ) {

	class YITH_WC_Subscription {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Subscription
		 */
		protected static $instance;

		/**
		 * Post name of subscription
		 *
		 * @var string
		 */
		public $post_name = 'ywsbs_subscription';

		/**
		 * @var bool
		 */
		public $debug_active = false;

		/**
		 * @var WC_Logger
		 */
		public $debug;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Subscription
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			// Common YITH hooks
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// Register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// check if subscription is enabled
			if ( get_option( 'ywsbs_enabled' ) != 'yes' ) {
				return;
			}

			if ( get_option( 'ywsbs_enable_log' ) == 'yes' ) {
				$this->debug_active = true;
				$this->debug        = new WC_Logger();
			}

			add_action( 'init', array( $this, 'init' ) );

			// custom styles and javascripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );

			/*Load Singleton istances*/
			YWSBS_Subscription_Helper();
			YITH_WC_Activity();

			YWSBS_Subscription_Cron();
			YWSBS_Subscription_Order();
			YWSBS_Subscription_Cart();
			YWSBS_Subscription_Coupons();
			YWSBS_Subscription_Paypal();

			yith_check_privacy_enabled() && YWSBS_Subscription_Privacy( true );

			// My Account Subscription Sections
			YWSBS_Subscription_My_Account();

			// Change product prices
			add_filter( 'woocommerce_get_price_html', array( $this, 'change_price_html' ), 10, 2 );
			add_filter( 'woocommerce_get_variation_price_html', array( $this, 'change_price_html' ), 10, 2 );

			// Add to cart label
			add_filter(
				'woocommerce_product_single_add_to_cart_text',
				array(
					$this,
					'change_add_to_cart_label',
				),
				99,
				2
			);
			add_filter( 'add_to_cart_text', array( $this, 'change_add_to_cart_label' ), 99 );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'change_add_to_cart_label' ), 99, 2 );
			add_filter( 'woocommerce_available_variation', array( $this, 'add_params_to_available_variation' ), 10, 3 );
			add_filter(
				'woocommerce_show_variation_price',
				array(
					$this,
					'show_variation_subscription_price',
				),
				30,
				3
			);

			// Ensure a subscription is never in the cart with products
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_item_validate' ), 10, 4 );
			add_action( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways' ) );

			// email settings
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );

			$this->load_plugin_integration();

			// Load integrated gateways
			$this->load_gateway_integration();

			$this->update_meta_free_premium();
		}

		/**
		 * Save main site URL if not set
		 *
		 * @access public
		 * @return void
		 *
		 * @since  1.0.0
		 */
		public function init() {
			load_plugin_textdomain( 'yith-woocommerce-subscription', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {

			if ( ! apply_filters( 'ywsbs_load_assets', true ) ) {
				return;
			}

			if ( is_account_page() ) {
				// Prettyphoto for modal questions
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', YITH_YWSBS_ASSETS_URL . '/css/prettyPhoto.css' );
				wp_enqueue_script( 'ywsbs-prettyPhoto', YITH_YWSBS_ASSETS_URL . '/js/jquery.prettyPhoto' . YITH_YWSBS_SUFFIX . '.js', array( 'jquery' ), false, true );

			}

			wp_enqueue_style( 'yith_ywsbs_frontend', YITH_YWSBS_ASSETS_URL . '/css/frontend.css', YITH_YWSBS_VERSION );
			wp_enqueue_script(
				'yith_ywsbs_frontend',
				YITH_YWSBS_ASSETS_URL . '/js/ywsbs-frontend' . YITH_YWSBS_SUFFIX . '.js',
				array(
					'jquery',
					'wc-add-to-cart-variation',
				),
				YITH_YWSBS_VERSION,
				true
			);

			wp_localize_script(
				'yith_ywsbs_frontend',
				'yith_ywsbs_frontend',
				array(
					'add_to_cart_label'  => get_option( 'ywsbs_add_to_cart_label' ),
					'default_cart_label' => apply_filters( 'ywsbs_add_to_cart_default_label', __( 'Add to cart', 'woocommerce' ) ),
				)
			);
		}


		/**
		 * Add custom params to variations
		 *
		 * @access public
		 *
		 * @param $args      array
		 * @param $product   object
		 * @param $variation object
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function add_params_to_available_variation( $args, $product, $variation ) {

			if ( $this->is_subscription( $variation->get_id() ) ) {
				$args['is_subscription'] = true;
			} else {
				$args['is_subscription'] = false;
			}

			$is_switchable = yit_get_prop( $variation, '_ywsbs_switchable', true );

			if ( $is_switchable == 'yes' ) {
				$args['is_switchable'] = true;
			} else {
				$args['is_switchable'] = false;
			}

			return $args;
		}


		/**
		 * Load YIT Plugin Framework
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}


		/**
		 * Change price HTML to the product
		 *
		 * @access public
		 *
		 * @param $product      WC_Product
		 *
		 * @param int                     $quantity
		 *
		 * @return string
		 * @since  1.2.0
		 */
		public function change_general_price_html( $product, $quantity = 1 ) {

			$signup_fee        = yit_get_prop( $product, '_ywsbs_fee' );
			$trial_period      = yit_get_prop( $product, '_ywsbs_trial_per' );
			$trial_time_option = yit_get_prop( $product, '_ywsbs_trial_time_option' );
			$price_is_per      = yit_get_prop( $product, '_ywsbs_price_is_per' );
			$price_time_option = yit_get_prop( $product, '_ywsbs_price_time_option' );
			$max_length        = yit_get_prop( $product, '_ywsbs_max_length' );

			$price_time_option_string = ywsbs_get_price_per_string( $price_is_per, $price_time_option );

			$price = 'incl' == get_option( 'woocommerce_tax_display_cart' ) ? wc_get_price_including_tax( $product ) : $product->get_price();

			$signup_fee = empty( $signup_fee ) ? '' : $quantity * $signup_fee;

			$price_html  = wc_price( $price * $quantity );
			$price_html .= '<span class="price_time_opt"> / ' . $price_time_option_string . '</span>';

			if ( ! $product->is_type( 'variable' ) && ( ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) || ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) || ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) ) ) {

				if ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) {
					$price_html .= __( ' for ', 'yith-woocommerce-subscription' ) . ywsbs_get_price_per_string( $max_length, $price_time_option );
				}

				$and         = false;
				$price_html .= ( $signup_fee || $trial_period ) ? '<span class="ywsbs-price-detail"> + ' : ' ';

				if ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) {
					$price_html .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
					$price_html .= apply_filters( 'ywsbs_signup_fee_label', __( ' a sign-up fee of ', 'yith-woocommerce-subscription' ) ) . wc_price( $signup_fee );
					$and         = true;
				}

				$t = ywsbs_get_price_per_string( $trial_period, $trial_time_option, true );

				if ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) {
					$price_html .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
					$price_html .= __( ' a free trial of ', 'yith-woocommerce-subscription' ) . $t;
				}

				$price_html .= ( $signup_fee || $trial_period ) ? '</span>' : '';
			}

			return apply_filters( 'ywsbs_change_general_price_html', $price_html, $product, $price_is_per, $price_time_option, $max_length, $signup_fee, $trial_period, $quantity );
		}

		/**
		 * Change price HTML to the product
		 *
		 * @access public
		 *
		 * @param $price        float
		 * @param $product      object
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function change_price_html( $price, $product, $order = null ) {

			if ( ! $this->is_subscription( $product ) || apply_filters( 'ywsbs_skip_price_html_filter', false, $product, $order ) ) {
				return apply_filters( 'ywsbs_skipped_price_html_filter', $price );
			}

			$signup_fee               = yit_get_prop( $product, '_ywsbs_fee' );
			$trial_period             = yit_get_prop( $product, '_ywsbs_trial_per' );
			$trial_time_option        = yit_get_prop( $product, '_ywsbs_trial_time_option' );
			$price_is_per             = yit_get_prop( $product, '_ywsbs_price_is_per' );
			$price_time_option        = yit_get_prop( $product, '_ywsbs_price_time_option' );
			$max_length               = yit_get_prop( $product, '_ywsbs_max_length' );
			$price_time_option_string = ywsbs_get_price_per_string( $price_is_per, $price_time_option );

			$currency = ! is_null( $order ) ? $order->get_order_currency() : get_woocommerce_currency();
			$price   .= '<span class="price_time_opt"> / ' . $price_time_option_string . '</span>';

			if ( ! $product->is_type( 'variable' ) && ( ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) || ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) || ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) ) ) {

				if ( $max_length && get_option( 'ywsbs_show_length_period' ) == 'yes' ) {
					$price .= __( ' for ', 'yith-woocommerce-subscription' ) . ywsbs_get_price_per_string( $max_length, $price_time_option, true );
				}

				$and    = false;
				$price .= ( $signup_fee || $trial_period ) ? '<span class="ywsbs-price-detail"> + '  : ' ';

				if ( $signup_fee && get_option( 'ywsbs_show_fee' ) == 'yes' ) {
					$price .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
					$price .= apply_filters( 'ywsbs_signup_fee_label', __( ' a sign-up fee of ', 'yith-woocommerce-subscription' ) ) . wc_price( $signup_fee, array( 'currency' => $currency ) );
					$and    = true;
				}

				$t = ywsbs_get_price_per_string( $trial_period, $trial_time_option, true );

				if ( $trial_period && get_option( 'ywsbs_show_trial_period' ) == 'yes' ) {
					$price .= $and ? __( ' and ', 'yith-woocommerce-subscription' ) : '';
					$price .= apply_filters( 'ywsbs_free_trial_label', __( ' a free trial of ', 'yith-woocommerce-subscription' ) . $t, $t );
				}

				$price .= ( $signup_fee || $trial_period ) ? '</span>' : '';
			}

			return apply_filters( 'ywsbs_change_price_html', $price, $product, $price_is_per, $price_time_option, $max_length, $signup_fee, $trial_period );
		}

		/**
		 * Check if a product is a subscription
		 *
		 * @access public
		 *
		 * @param $product
		 *
		 * @return bool
		 * @internal param int|WC_Product $product_id
		 *
		 * @since  1.0.0
		 */
		public function is_subscription( $product ) {
			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! $product ) {
				return false;
			}
			$is_subscription = yit_get_prop( $product, '_ywsbs_subscription' );
			$price_is_per    = yit_get_prop( $product, '_ywsbs_price_is_per' );

			return apply_filters( 'ywsbs_is_subscription', ( $is_subscription == 'yes' && $price_is_per != '' ) ? true : false, yit_get_prop( $product, 'id' ) );
		}

		/**
		 * Check if in the cart there are subscription that needs shipping
		 *
		 * @access public
		 * @return bool
		 * @since  1.0.0
		 */
		public function cart_has_subscription_with_shipping() {

			$cart_has_subscription_with_shipping = false;
			$cart_contents                       = WC()->cart->get_cart();

			if ( ! isset( $cart_contents ) || empty( $cart_contents ) ) {
				return $cart_has_subscription_with_shipping;
			}

			foreach ( $cart_contents as $cart_item ) {
				/** @var WC_Product $product */
				$product = $cart_item['data'];
				if ( $this->is_subscription( $product ) && $product->needs_shipping() ) {
					$cart_has_subscription_with_shipping = true;
				}
			}

			return apply_filters( 'ywsbs_cart_has_subscription_with_shipping', $cart_has_subscription_with_shipping );
		}

		/**
		 * Only a subscription can be added to the cart this method check if there's
		 * a subscription in cart and remove the element if the next product to add is another subscription
		 *
		 * @param $valid        bool
		 * @param $product_id   int
		 * @param $quantity     int
		 * @param $variation_id int
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function cart_item_validate( $valid, $product_id, $quantity, $variation_id = 0 ) {

			if ( ywsbs_enable_subscriptions_multiple() ) {
				return $valid;
			}

			$id = ( ! empty( $variation_id ) ) ? $variation_id : $product_id;
			/** @var WC_Product $product */
			$product = wc_get_product( $id );

			if ( $this->is_subscription( $product ) && $item_keys = $this->cart_has_subscriptions() ) {
				if ( $item_keys ) {
					foreach ( $item_keys as $item_key ) {
						$current_item = WC()->cart->get_cart_item( $item_key );
						if ( ! empty( $current_item ) ) {
							$item_id = ( ! empty( $current_item['variation_id'] ) ) ? $current_item['variation_id'] : $current_item['product_id'];

							if ( $item_id != $id ) {
								$this->clean_cart_from_subscriptions( $item_key );
								$message = __( 'A subscription has been removed from your cart. You cannot purchase different subscriptions at the same time.', 'yith-woocommerce-subscription' );
								wc_add_notice( $message, 'notice' );
							}
						}
					}
				}
			}

			return $valid;
		}


		/**
		 * Disable gateways that don't support multiple subscription on cart
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function disable_gateways( $gateways ) {
			if ( WC()->cart && is_checkout() ) {
				$subscription_on_cart = $this->cart_has_subscriptions();
				if ( is_array( $subscription_on_cart ) && count( $subscription_on_cart ) >= 2 && WC()->payment_gateways() ) {
					foreach ( $gateways as $gateway_id => $gateway ) {
						if ( ! $gateway->supports( 'yith_subscriptions_multiple' ) ) {
							unset( $gateways[ $gateway_id ] );
						}
					}
				}
			}

			return $gateways;
		}


		/**
		 * Removes all subscription products from the shopping cart.
		 *
		 * @param $item_key int
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function clean_cart_from_subscriptions( $item_key ) {
			WC()->cart->set_quantity( $item_key, 0 );
		}

		/**
		 * Check if in the cart there are subscription
		 *
		 * @return bool/array
		 * @since  1.0.0
		 */
		public function cart_has_subscriptions() {
			$contents = WC()->cart->get_cart();
			$items    = array();
			$count    = 0;
			if ( ! empty( $contents ) ) {
				foreach ( $contents as $item_key => $item ) {
					$product = $item['data'];
					if ( $this->is_subscription( $product ) ) {
						$count = array_push( $items, $item_key );
					}
				}
			}

			return $count == 0 ? false : $items;
		}

		/**
		 * Check if in the order there are subscription
		 *
		 * @param $order
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function order_has_subscription( $order ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			$order_items = $order->get_items();
			if ( empty( $order_items ) ) {
				return false;
			}

			foreach ( $order_items as $key => $order_item ) {
				$id = ( $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];

				if ( YITH_WC_Subscription()->is_subscription( $id ) ) {
					return true;
				}
			}

			return false;
		}


		/**
		 * Change add to cart label in subscription product
		 *
		 * @param $label      float
		 * @param $product    object
		 *
		 * @return bool/int
		 * @since  1.0.0
		 */
		public function change_add_to_cart_label( $label, $product = null ) {
			$new_label = get_option( 'ywsbs_add_to_cart_label' );

			if ( is_null( $product ) ) {
				global $product;
				if ( is_null( $product ) ) {
					global $post;
					if ( empty( $post ) ) {
						return;
					}
					$product = wc_get_product( $post->ID );
				}
			}

			if ( is_null( $product ) || ! is_object( $product ) ) {
				return;
			}

			$id = $product->get_id();

			if ( $product->is_type( 'variable' ) ) {
				$attributes = $product->get_default_attributes();

				$default_attributes = array();
				foreach ( $attributes as $key => $value ) {
					$default_attributes[ 'attribute_' . $key ] = $value;
				}

				$data_store = WC_Data_Store::load( 'product' );
				$id         = $data_store->find_matching_product_variation( $product, $default_attributes );
			}

			if ( $id && $new_label && $this->is_subscription( $id ) ) {
				$label = $new_label;
			}

			return $label;
		}

		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function my_account_subscriptions() {
			wc_get_template( 'myaccount/my-subscriptions-view.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function my_account_subscriptions_shortcode() {
			ob_start();
			wc_get_template( 'myaccount/my-subscriptions-view.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );

			return ob_get_clean();
		}

		/**
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function my_account_edit_address() {
			global $wp;
			print_r( $wp->query_vars );

			if ( isset( $_GET['subscription'] ) ) {
				$subscription = ywsbs_get_subscription( $_GET['subscription'] );
				if ( get_current_user_id() == $subscription->user_id ) {
					echo '<p>' . esc_html__( 'Both the shipping address used for the subscription and your default shipping address for future purchases will be updated.', 'yith-woocommerce-subscription' );
					echo '<input type="hidden" name="ywsbs_edit_address_to_subscription" value="' . absint( $_GET['subscription'] ) . '" id="ywsbs_edit_address_to_subscription" />';
				}
			} elseif ( isset( $_GET['address'] ) || ( ( isset( $wp->query_vars['edit-address'] ) && ! empty( $wp->query_vars['edit-address'] ) ) ) ) {
				woocommerce_form_field(
					'change_subscriptions_addresses',
					array(
						'type'  => 'checkbox',
						'class' => array( 'form-row-wide' ),
						'label' => __( 'Update this address also for my active subscriptions', 'yith-woocommerce-subscription' ),
					)
				);
			}

			wp_nonce_field( 'ywsbs_edit_address', '_ywsbs_edit' );
		}

		/**
		 * @param $user_id
		 * @param $load_address
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function my_account_save_address( $user_id, $load_address ) {
			if ( isset( $_REQUEST['ywsbs_edit_address_to_subscription'] ) ) {
				// todo: edit the address to single subscription
			} elseif ( isset( $_REQUEST['change_subscriptions_addresses'] ) ) {
				// todo: edit the address to all subscriptions
			}
		}


		/**
		 * Add subscription section to my-account page
		 *
		 * @since   1.0.0
		 *
		 * @param $order
		 */
		public function subscriptions_related( $order ) {
			wc_get_template( 'myaccount/subscriptions-related.php', array( 'order' => $order ), '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the endpoint for the page in my account to manage the subscription view
		 *
		 * @since 1.0.0
		 */
		public function add_endpoint() {
			WC()->query->query_vars['view-subscription'] = get_option( 'woocommerce_myaccount_view_subscription_endpoint', 'view-subscription' );
		}

		/**
		 * Load the page of subscription
		 *
		 * @since 1.0.0
		 */
		public function load_subscription_detail_page() {
			global $wp, $post;

			if ( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars['view-subscription'] ) ) {
				return;
			}

			$subscription_id    = $wp->query_vars['view-subscription'];
			$post->post_title   = sprintf( __( 'Subscription #%s', 'yith-woocommerce-subscription' ), $subscription_id );
			$post->post_content = WC_Shortcodes::shortcode_wrapper( array( $this, 'view_subscription' ) );

			remove_filter( 'the_content', 'wpautop' );
		}

		/**
		 * Show the quote detail
		 *
		 * @since 1.0.0
		 */
		public function view_subscription() {
			global $wp;
			if ( ! is_user_logged_in() ) {
				wc_get_template( 'myaccount/form-login.php', null, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
			} else {
				$subscription_id = $wp->query_vars['view-subscription'];
				$subscription    = new YWSBS_Subscription( $subscription_id );
				wc_get_template(
					'myaccount/view-subscription.php',
					array(
						'subscription' => $subscription,
						'user'         => get_user_by( 'id', get_current_user_id() ),
					),
					'',
					YITH_YWSBS_TEMPLATE_PATH . '/'
				);
			}
		}


		/**
		 * Start the downgrade process
		 *
		 * @param int                $from_id current variation id
		 * @param int                $to_id variation to switch
		 * @param YWSBS_Subscription $subscription current subscription
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return void
		 * @throws Exception
		 */
		public function downgrade_process( $from_id, $to_id, $subscription ) {
			// retrieve the days left to the next payment or to the expiration data
			$left_time = $subscription->get_left_time_to_next_payment();
			$days      = ywsbs_get_days( $left_time );

			if ( $left_time <= 0 && $days > 1 ) {
				add_user_meta(
					$subscription->user_id,
					'ywsbs_upgrade_' . $to_id,
					array(
						'subscription_id' => $subscription->id,
						'pay_gap'         => 0,
					)
				);
			} elseif ( $left_time > 0 ) {
				add_user_meta( $subscription->user_id, 'ywsbs_downgrade_' . $to_id, $subscription->id );
				add_user_meta(
					$subscription->user_id,
					'ywsbs_trial_' . $to_id,
					array(
						'subscription_id' => $subscription->id,
						'trial_days'      => $days,
					)
				);
			}

			$variation = wc_get_product( $to_id );

			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $subscription->product_id, $subscription->quantity, $to_id, $variation->get_variation_attributes() ) ) {
				wc_add_notice( __( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}
			WC()->cart->add_to_cart( $subscription->product_id, $subscription->quantity, $to_id, $variation->get_variation_attributes() );

			$checkout_url = wc_get_checkout_url();

			wp_redirect( $checkout_url );
			exit;
		}

		/**
		 * Start the upgrade process
		 *
		 * @param int                $from_id current variation id
		 * @param int                $to_id variation to switch
		 * @param YWSBS_Subscription $subscription current subscription
		 *
		 * @param                    $pay_gap
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @throws Exception
		 */
		public function upgrade_process( $from_id, $to_id, $subscription, $pay_gap ) {

			add_user_meta(
				$subscription->user_id,
				'ywsbs_upgrade_' . $to_id,
				array(
					'subscription_id' => $subscription->id,
					'pay_gap'         => $pay_gap,
				),
				true
			);

			$variation = wc_get_product( $to_id );

			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $subscription->product_id, $subscription->quantity, $to_id, $variation->get_variation_attributes() ) ) {
				wc_add_notice( __( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}
			WC()->cart->add_to_cart( $subscription->product_id, $subscription->quantity, $to_id, $variation->get_variation_attributes() );

			$checkout_url = wc_get_checkout_url();
			wp_redirect( $checkout_url );
			exit;

		}

		/**
		 * Cancel the subscription
		 *
		 * @param int $subscription_id subscription to cancel
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return bool
		 */
		public function cancel_subscription_after_upgrade( $subscription_id ) {

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
				$this->add_notice( __( 'This subscription cannot be cancelled. You cannot switch to related subscriptions', 'yith-woocommerce-subscription' ), 'error' );

				return false;
			}

			$subscription->update_status( 'cancelled', 'customer' );
			$subscription->status = 'cancelled';
			do_action( 'ywsbs_subscription_cancelled_mail', $subscription );

			YITH_WC_Activity()->add_activity( $subscription->id, 'switched', 'success', 0, __( 'Subscription cancelled due to switch', 'yith-woocommerce-subscription' ) );
		}

		/**
		 * Update the old subscription after the downgrade
		 *
		 * @param int $subcription_id subscription to update
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return bool
		 */
		public function update_subscription_after_downgrade( $subcription_id ) {

			$subscription = ywsbs_get_subscription( $subcription_id );

			if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
				$this->add_notice( __( 'This subscription cannot be cancelled. You cannot switch to a related subscription', 'yith-woocommerce-subscription' ), 'error' );

				return false;
			}

			// the current subscription will be expired to the last payment
			if ( $subscription->payment_due_date ) {
				$subscription->set( 'expired_date', $subscription->payment_due_date );
				$subscription->set( 'payment_due_date', false );
				YITH_WC_Activity()->add_activity( $subscription->id, 'switched', 'success', $subscription->order_id, __( 'Expiration date of this subscription changed due to downgrade', 'yith-woocommerce-subscription' ) );
			} else {
				YITH_WC_Activity()->add_activity( $subscription->id, 'switched', 'success', $subscription->order_id, __( 'Subscription will be forced to expire due to downgrade', 'yith-woocommerce-subscription' ) );
			}

			// if there's a pending order for this subscription change the status of the order to cancelled
			if ( $subscription->renew_order ) {
				$order = wc_get_order( $subscription->renew_order );
				if ( $order ) {
					$order->update_status( 'cancelled' );
					$order->add_order_note( sprintf( __( 'This order has been cancelled because subscription #%d has been downgraded', 'yith-woocommerce-subscription' ), $subscription->id ) );
				}
			}
		}

		/**
		 * Renew the subscription
		 *
		 * @param YWSBS_Subscription $subscription subscription to renew
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return void
		 * @throws Exception
		 */
		public function renew_the_subscription( $subscription ) {
			WC()->cart->add_to_cart( $subscription->product_id, $subscription->quantity, $subscription->variation_id );
		}

		/**
		 * Change the status of subscription manually
		 *
		 * @param string             $new_status
		 * @param YWSBS_Subscription $subscription
		 * @param string             $from
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @return bool
		 */
		public function manual_change_status( $new_status, $subscription, $from = '' ) {
			switch ( $new_status ) {
				case 'active':
					if ( ! $subscription->can_be_active() ) {

						$this->add_notice( __( 'This subscription cannot be activated', 'yith-woocommerce-subscription' ), 'error' );
					} else {

						// change the status to cancelled
						$subscription->update_status( 'active', $from );
						$this->add_notice( __( 'This subscription is now active', 'yith-woocommerce-subscription' ), 'success' );
					}

					break;
				case 'overdue':
					if ( ! $subscription->can_be_overdue() ) {
						$this->add_notice( __( 'This subscription cannot be in status overdue', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// change the status to cancelled
						$subscription->update_status( 'overdue', $from );
						$this->add_notice( __( 'This subscription is now in overdue status', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;
				case 'suspended':
					if ( ! $subscription->can_be_suspended() ) {
						$this->add_notice( __( 'This subscription cannot be in status suspended', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// change the status to cancelled
						$subscription->update_status( 'suspended', $from );
						$this->add_notice( __( 'This subscription is now suspended', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;
				case 'cancelled':
					if ( ! $subscription->can_be_cancelled() ) {
						$this->add_notice( __( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );
					} else {

						// filter added to gateway payments
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$this->add_notice( __( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						// change the status to cancelled
						$subscription->update_status( 'cancelled', $from );

						$this->add_notice( __( 'This subscription is now cancelled', 'yith-woocommerce-subscription' ), 'success' );

					}
					break;
				case 'cancel-now':
					if ( ! $subscription->can_be_cancelled() ) {
						$this->add_notice( __( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );
					} else {

						// filter added to gateway payments
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$this->add_notice( __( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						// change the status to cancelled
						$subscription->update_status( 'cancel-now', $from );
						$this->add_notice( __( 'This subscription is now cancelled', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;
				case 'paused':
					if ( ! $subscription->can_be_paused() ) {
						$this->add_notice( __( 'This subscription cannot be paused', 'yith-woocommerce-subscription' ), 'error' );
					} else {

						// filter added to gateway payments
						if ( ! apply_filters( 'ywsbs_suspend_recurring_payment', true, $subscription ) ) {
							$this->add_notice( __( 'This subscription cannot be paused', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						$result               = $subscription->update_status( 'paused', $from );
						$subscription->status = 'paused';
						$this->add_notice( __( 'This subscription is now paused', 'yith-woocommerce-subscription' ), 'success' );

					}
					break;
				case 'resumed':
					if ( ! $subscription->can_be_resumed() ) {
						$this->add_notice( __( 'This subscription cannot be resumed', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// filter added to gateway payments
						if ( ! apply_filters( 'ywsbs_resume_recurring_payment', true, $subscription ) ) {
							$this->add_notice( __( 'This subscription cannot be resumed', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}
						$subscription->update_status( 'resume', $from );
						$subscription->status = 'active';
						$this->add_notice( __( 'This subscription is now active', 'yith-woocommerce-subscription' ), 'success' );
					}

					break;
				default:
			}

		}

		/**
		 * Return overdue time period
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function overdue_time() {
			if ( get_option( 'ywsbs_enable_overdue_period' ) != 'yes' && get_option( 'ywsbs_overdue_period' ) == '' ) {
				return false;
			} else {
				return intval( get_option( 'ywsbs_overdue_period' ) ) * 84600;
			}
		}

		/**
		 * Return suspension time period
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function suspension_time() {
			if ( get_option( 'ywsbs_enable_suspension_period' ) != 'yes' && get_option( 'ywsbs_suspension_period' ) == '' ) {
				return false;
			} else {
				return intval( get_option( 'ywsbs_suspension_period' ) ) * 84600;
			}
		}

		/**
		 * Print a message in my account page
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $message
		 * @param $type
		 */
		public function add_notice( $message, $type ) {
			if ( ! is_admin() ) {
				wc_add_notice( $message, $type );
			}
		}

		/**
		 * Filters woocommerce available mails
		 *
		 * @param $emails array
		 *
		 * @access public
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function add_woocommerce_emails( $emails ) {
			require_once YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription.php';
			$emails['YITH_WC_Subscription_Status']                   = include YITH_YWSBS_INC . 'emails/class.yith-wc-subscription-status.php';
			$emails['YITH_WC_Customer_Subscription_Cancelled']       = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-cancelled.php';
			$emails['YITH_WC_Customer_Subscription_Suspended']       = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-suspended.php';
			$emails['YITH_WC_Customer_Subscription_Expired']         = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-expired.php';
			$emails['YITH_WC_Customer_Subscription_Before_Expired']  = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-before-expired.php';
			$emails['YITH_WC_Customer_Subscription_Paused']          = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-paused.php';
			$emails['YITH_WC_Customer_Subscription_Resumed']         = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-resumed.php';
			$emails['YITH_WC_Customer_Subscription_Request_Payment'] = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-request-payment.php';
			$emails['YITH_WC_Customer_Subscription_Renew_Reminder']  = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-renew-reminder.php';
			$emails['YITH_WC_Customer_Subscription_Payment_Done']    = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-payment-done.php';
			$emails['YITH_WC_Customer_Subscription_Payment_Failed']  = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-payment-failed.php';

			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function load_wc_mailer() {

			// Administrator
			add_action( 'ywsbs_subscription_admin_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );

			// Customers
			add_action(
				'ywsbs_customer_subscription_cancelled_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_expired_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_before_expired_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_suspended_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_resumed_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_paused_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_request_payment_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_renew_reminder_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_payment_done_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_payment_failed_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);

		}


		/**
		 * Checks if the WordPress site URL has changed for the site subscriptions.
		 * Useful for checking if automatic payments should be processed.
		 *
		 * @access public
		 * @return void
		 *
		 * @since  1.0.0
		 */
		public function check_different_url() {

			$has_changed = ( get_site_url() !== $this->get_site_url() ) ? true : false;

			return apply_filters( 'ywsbs_has_different_url', $has_changed );
		}

		/**
		 * Get main site URL with placeholder in the middle
		 *
		 * @access public
		 * @return string
		 *
		 * @since  1.0.0
		 */
		public function get_site_url() {
			$current_site_url = get_site_url();

			return substr_replace( $current_site_url, '***YWSBS***', strlen( $current_site_url ) / 2, 0 );
		}


		/**
		 * Check if is main site URL so we can disable some actions on sandox websites
		 *
		 * @access public
		 * @return bool
		 */
		public function is_main_site() {

			$is_main_site = ! ( defined( 'WP_ENV' ) && WP_ENV );

			$current_site_url = get_site_url();

			// Make sure we have saved original URL, otherwise treat as duplicate site
			$ywsbs_site_url = get_option( 'ywsbs_site_url' );
			if ( ! empty( $ywsbs_site_url ) ) {
				$main_site_url = set_url_scheme( str_replace( '***YWSBS***', '', get_option( 'ywsbs_site_url' ) ) );
				$is_main_site  = ( $current_site_url == $main_site_url ) ? true : false;
			}

			return $is_main_site;
		}


		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YWSBS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YWSBS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWSBS_INIT, YITH_YWSBS_SECRET_KEY, YITH_YWSBS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_YWSBS_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWSBS_SLUG, YITH_YWSBS_INIT );
		}


		/**
		 * Update the post meta when a free premium version was installed before the premium.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function update_meta_free_premium() {

			if ( get_option( 'ywsbs_activation_plugin_action' ) && apply_filters( 'update_meta_free_premium', true ) ) {
				return;
			}

			$posts = get_posts(
				array(
					'post_type'      => 'ywsbs_subscription',
					'posts_per_page' => - 1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			if ( $posts ) {
				global $wpdb;
				$dates = array( '_expired_date', '_start_date', '_payment_due_date', '_cancelled_date' );
				foreach ( $dates as $date ) {
					$query = "UPDATE $wpdb->postmeta as ypm SET ypm.meta_value = UNIX_TIMESTAMP(ypm.meta_value) WHERE ypm.meta_key LIKE '$date' AND ypm.meta_value NOT LIKE '' AND ypm.post_id IN (" . implode( ',', $posts ) . ')';
					$wpdb->query( $query );
				}

				$query = "UPDATE $wpdb->postmeta as ypm SET ypm.meta_key = SUBSTRING( ypm.meta_key, 2) WHERE ypm.meta_key LIKE '\_%' AND ypm.post_id IN (" . implode( ',', $posts ) . ')';
				$wpdb->query( $query );
			}

			add_option( 'ywsbs_activation_plugin_action', 1 );
		}

		/**
		 * Return the ids of user's subscription
		 *
		 * @param $user_id
		 * @param $status
		 *
		 * @return array|int
		 */
		public function get_user_subscriptions( $user_id, $status = '' ) {

			$args = array(
				'post_type'      => 'ywsbs_subscription',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'     => 'user_id',
						'value'   => $user_id,
						'compare' => '=',
					),
				),

			);

			if ( ! empty( $status ) ) {
				$args['meta_query'][] = array(
					'key'     => 'status',
					'value'   => $status,
					'compare' => 'LIKE',
				);
			}

			$posts = get_posts( $args );

			return $posts ? wp_list_pluck( $posts, 'ID' ) : 0;
		}

		/**
		 * if the product is a variation, show the price
		 *
		 * @param $show
		 * @param $variable
		 * @param $variation
		 *
		 * @return bool
		 */
		public function show_variation_subscription_price( $show, $variable, $variation ) {

			if ( $this->is_subscription( $variation ) ) {
				$show = true;
			}

			return $show;
		}

		/**
		 * Register the message on plugin log
		 *
		 * @param string $message
		 *
		 * @since 1.4.6
		 */
		public function log( $message ) {
			if ( YITH_WC_Subscription()->debug_active ) {
				YITH_WC_Subscription()->debug->add( 'ywsbs', $message );
			}
		}

		/**
		 * Load the classes that support different plugins integration
		 */
		private function load_plugin_integration() {

			// YITH WooCommerce Multivendor compatibility
			if ( defined( 'YITH_WPV_PREMIUM' ) ) {
				require_once YITH_YWSBS_INC . 'compatibility/yith-woocommerce-product-vendors.php';
				YWSBS_Multivendor();
			}

			// YITH WooCommerce Membership compatibility
			if ( defined( 'YITH_WCMBS_PREMIUM' ) ) {
				require_once YITH_YWSBS_INC . 'compatibility/yith-woocommerce-membership.php';
				YWSBS_Membership();
			}
		}

		/**
		 * Load the classes that support different gateway integration
		 */
		private function load_gateway_integration() {
			// WooCommerce Stripe Gateway compatibility
			if ( class_exists( 'WC_Stripe' ) && version_compare( WC_STRIPE_VERSION, '4.1.11', '>' ) ) {
				require_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-stripe/class.yith-wc-stripe-integration.php';
				require_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-stripe/class.yith-wc-subscription-wc-stripe.php';
				YITH_WC_Stripe_Integration::instance();
			}
		}


	}
}

/**
 * Unique access to instance of YITH_WC_Subscription class
 *
 * @return \YITH_WC_Subscription
 */
function YITH_WC_Subscription() {
	return YITH_WC_Subscription::get_instance();
}
