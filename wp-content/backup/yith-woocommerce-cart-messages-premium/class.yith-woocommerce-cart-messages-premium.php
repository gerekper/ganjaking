<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWCM_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of Yit WooCommerce Cart Messages
 *
 * @class   YWCM_Cart_Messages
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */
if ( ! class_exists( 'YWCM_Cart_Messages_Premium' ) ) {

	/**
	 * Class YWCM_Cart_Messages_Premium
	 */
	class YWCM_Cart_Messages_Premium extends YWCM_Cart_Messages {

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			parent::__construct();

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			//save referer of visitors
			add_action( 'wp_loaded', array( $this, 'save_referer_host' ), 1 );
			add_filter( 'yith_ywcm_is_valid_message', array( $this, 'is_valid_user' ), 10, 2 );
			add_filter( 'yith_ywcm_is_valid_message', array( $this, 'is_valid_page' ), 10, 3 );
			add_filter( 'yith_ywcm_is_valid_message', array( $this, 'is_valid_extra_check' ), 10, 3 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 20 );

			add_action( 'wc_ajax_ywcm_update_cart_messages', array( $this, 'ywcm_update_cart_messages' ) );

			if( class_exists('WC_Aelia_CurrencySwitcher')){
				YWCM_Aelia_Integration();
			}
		}

		/**
		 *
		 */
		public function ywcm_update_cart_messages() {

			if ( ! isset( $_POST['ywcm_id'] ) ) {
				$result = false;
				wp_send_json(
					array(
						'result' => $result
					)
				);
			}


			$result     = true;
			$message_id = $_POST['ywcm_id'];

			$message = $this->get_message( $message_id, true );

			$template = apply_filters( 'ywcm_get_message_template', $this->call_message_template( $message ), $message );

			wp_send_json(
				array(
					'result'  => $result,
					'message' => $template
				)
			);


		}

		/**
		 * Enqueue style scripts
		 *
		 * Enqueue style and scripts files
		 *
		 * @return  void
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function enqueue_styles_scripts() {
			if ( 'yes' == get_option( 'ywcm_show_in_cart', 'yes' ) && is_cart() ||
			     'yes' == get_option( 'ywcm_show_in_checkout', 'yes' ) && is_checkout() ||
			     'yes' == get_option( 'ywcm_show_in_shop_page', 'no' ) && is_shop() ||
			     'yes' == get_option( 'ywcm_show_in_single_product', 'no' ) && is_product() ||
			     apply_filters( 'yith_ywcm_enqueue_everywhere', false )
			) {
				wp_enqueue_style( 'yith_ywcm', YITH_YWCM_ASSETS_URL . '/css/style.css' );
				$custom_css = require_once( YITH_YWCM_TEMPLATE_PATH . '/layouts/css_layout.php' );
				wp_add_inline_style( 'yith_ywcm', $custom_css );

				wp_enqueue_script( 'ywcm_frontend', YITH_YWCM_ASSETS_URL . '/js/ywcm-frontend' . YITH_YWCM_SUFFIX . '.js', array(
					'jquery',
					'wc-add-to-cart-variation'
				), YITH_YWCM_VERSION, true );

				$script_params = array(
					'ajax_url'    => admin_url( 'admin-ajax' ) . '.php',
					'wc_ajax_url' => WC_AJAX::get_endpoint( "%%endpoint%%" ),
				);

				wp_localize_script( 'ywcm_frontend', 'yith_cm_general', $script_params );
			}
		}

		/**
		 * Enqueue style scripts in administrator
		 *
		 * Enqueue style and scripts files
		 *
		 * @return  void
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function admin_enqueue_scripts() {

			if ( get_post_type() != 'ywcm_message' ) {
				return;
			}

			wp_enqueue_style( 'yith_ywcm', YITH_YWCM_ASSETS_URL . '/css/admin.css' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'ywcm_timepicker', YITH_YWCM_ASSETS_URL . '/js/jquery-ui-timepicker-addon.min.js', array( 'jquery' ), YITH_YWCM_VERSION, true );
			wp_enqueue_script( 'yith_ywcm_admin', YITH_YWCM_ASSETS_URL . '/js/ywcm-admin' . YITH_YWCM_SUFFIX . '.js', array( 'ywcm_timepicker' ), YITH_YWCM_VERSION, true );

			if ( ! wp_script_is( 'selectWoo' ) ) {

				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
			}
		}

		/**
		 * Get Minimum Amount Args
		 *
		 * Return an array with the args to print into message or false if the message can't be print
		 *
		 * @return   mixed array || bool if the message can't be print
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */
		public function get_minimum_amount_args( $message ) {

			global $WOOCS;

			$args = array();

			$args['text']        = get_post_meta( $message->ID, '_ywcm_message_minimum_amount_text', true );
			$exclude_coupons     = get_post_meta( $message->ID, '_ywcm_minimum_amount_exclude_coupons', true );
			$products_to_exclude = get_post_meta( $message->ID, '_ywcm_minimum_amount_products_exclude', true );

			//add the single variations
			if ( $products_to_exclude ) {
				$products_to_exclude = (array) $products_to_exclude;

				$new_prod_to_exclude = $products_to_exclude;
				foreach ( $products_to_exclude as $product_exc_id ) {
					$product_exc = wc_get_product( $product_exc_id );

					if ( $product_exc instanceof WC_Product && $product_exc->is_type( 'variable' ) ) {
						$new_prod_to_exclude = array_merge( $new_prod_to_exclude, $product_exc->get_children() );
					}
				}
				$products_to_exclude = $new_prod_to_exclude;
			}

			if ( is_array( $products_to_exclude ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];

					if ( in_array( $_product->get_id(), $products_to_exclude ) ) {
						return false;
					}
				}
			}

			if ( $args['text'] == '' ) {
				return false;
			}

			$total_cart = apply_filters( 'ywcm_cart_total', $this->cart_total( $exclude_coupons ) );

			$minimum_amount = apply_filters( 'ywcm_message_minimum_amount', get_post_meta( $message->ID, '_ywcm_message_minimum_amount', true ) );
			$store_currency = get_option( 'woocommerce_currency' );

			if ( ! is_null( $WOOCS ) && $WOOCS->current_currency != $store_currency ) {
				$currencies     = $WOOCS->get_currencies();
				$value          = $minimum_amount * $currencies[ $WOOCS->current_currency ]['rate'];
				$minimum_amount = number_format( $value, 2, $WOOCS->decimal_sep, '' );
			}

			$threshold_amount = apply_filters( 'ywcm_minimum_amount_threshold_amount', get_post_meta( $message->ID, '_ywcm_minimum_amount_threshold_amount', true ) );

			if ( $minimum_amount == '' || $total_cart >= $minimum_amount ) {
				return false;
			}

			if ( $threshold_amount != '' && $total_cart < $threshold_amount ) {
				return false;
			}

			$remain_amount = wc_price( $minimum_amount - $total_cart );

			$args['text'] = str_replace( '{remaining_amount}', $remain_amount, $args['text'] );
			$name         = ywcm_get_current_user_name();
			if ( ! empty( $name ) ) {
				$args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
			}

			$args['button']  = $this->get_button_options( $message->ID );
			$args['slug']    = $message->post_name;
			$args['ywcm_id'] = $message->ID;

			return $args;

		}

		/**
		 * Get Referer Args
		 *
		 * Return an array with the args to print into message or false if the message can't be print
		 *
		 * @return   mixed array || bool if the message can't be print
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function get_referer_args( $message ) {

			$args = array();

			if ( ! isset( $_SESSION['yit_woocomerce_cart_message_referer_host'] ) ) {
				return false;
			}

			$args['text'] = get_post_meta( $message->ID, '_ywcm_message_referer_text', true );

			if ( $args['text'] == '' ) {
				return false;
			}

			// get the referer
			$referer_host = $_SESSION['yit_woocomerce_cart_message_referer_host'];
			$referer      = get_post_meta( $message->ID, '_ywcm_message_referer', true );
			$referer      = strpos( $referer, '://' ) === false ? 'http://' . $referer : $referer;
			$ref_urlhost  = parse_url( $referer, PHP_URL_HOST );
			//check the referer
			if ( $referer_host !== $ref_urlhost ) {
				return false;
			}

			$args['button'] = $this->get_button_options( $message->ID );
			$args['slug']   = $message->post_name;

			return $args;

		}

		/**
		 * Deadline
		 *
		 * Return an array with the args to print into message or false if the message can't be print
		 *
		 * @return   mixed array || bool if the message can't be print
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function get_deadline_args( $message ) {

			$args = array();

			$args['text'] = get_post_meta( $message->ID, '_ywcm_message_deadline_text', true );
			if ( $args['text'] == '' ) {
				return false;
			}
			$start_hour    = get_post_meta( $message->ID, '_ywcm_message_start_hour', true );
			$start_hour    = empty( $start_hour ) ? 0 : $start_hour;
			$deadline_hour = get_post_meta( $message->ID, '_ywcm_message_deadline_hour', true );
			$deadline_days = get_post_meta( $message->ID, '_ywcm_message_deadline_days', true );
			if ( $deadline_hour == '' && $deadline_days == '' ) {
				return false;
			}

			$now             = current_time( 'timestamp' );
			$now_day_of_week = date( 'w', $now );

			if ( is_array( $deadline_days ) && ! in_array( $now_day_of_week, $deadline_days ) ) {
				return false;
			}

			$now_minutes           = (int) date( 'G', current_time( 'timestamp' ) ) * 60 + (int) date( 'i', current_time( 'timestamp' ) );
			$start_hour_minutes    = $start_hour * 60;
			$deadline_hour_minutes = $deadline_hour * 60;

			if ( $now_minutes >= $deadline_hour_minutes || $now_minutes < $start_hour_minutes ) {
				return false;
			}

			$minutes_remaining = $deadline_hour_minutes - $now_minutes;
			$hours_to_show     = floor( $minutes_remaining / 60 );
			$minutes_to_show   = $minutes_remaining % 60;

			$time_remain = '';
			if ( $hours_to_show > 0 ) {
				$time_remain .= sprintf( _n( '%d hour', '%d hours', $hours_to_show, 'yith-woocommerce-cart-messages' ), $hours_to_show );
			}
			if ( $minutes_to_show > 0 ) {
				$time_remain .= ( $time_remain ? ' ' : '' ) . sprintf( _n( '%d minute', '%d minutes', $minutes_to_show, 'yith-woocommerce-cart-messages' ), $minutes_to_show );
			}

			$args['text'] = str_replace( '{time_remain}', $time_remain, $args['text'] );
			$name         = ywcm_get_current_user_name();
			if ( ! empty( $name ) ) {
				$args['text'] = str_replace( '{user_name}', ' ' . ywcm_get_current_user_name(), $args['text'] );
			}

			$args['button'] = $this->get_button_options( $message->ID );
			$args['slug']   = $message->post_name;

			return $args;

		}

		/**
		 * Save Referer
		 *
		 * Store the referer host in the client session
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */

		public function save_referer_host() {

			//return false is there's a direct request or there aren't referer_messages()
			if ( ! isset( $_SERVER['HTTP_REFERER'] ) || ! $this->have_referer_messages() ) {
				return false;
			}

			//start the session if is not active
			if ( ! session_id() ) {
				session_start();
			}

			$referer_host = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );


			if ( $referer_host && $referer_host != parse_url( site_url(), PHP_URL_HOST ) ) {
				$_SESSION['yit_woocomerce_cart_message_referer_host'] = $referer_host;
			}
		}

		/**
		 * Have referer messages
		 *
		 * Check if there are referer messages
		 *
		 * @return   bool
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function have_referer_messages() {

			foreach ( $this->messages as $message ) {
				$message_type = get_post_meta( $message->ID, '_ywcm_message_type', true );
				if ( $message_type == 'referer' ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Cart total
		 *
		 * Return the total of cart
		 *
		 * @return   float
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		public function cart_total( $exclude_coupons = false ) {

			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				$cart_total = ( ! WC()->cart->prices_include_tax ) ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal;
			} else {
				$cart_total = ( 'incl' == WC()->cart->tax_display_cart ) ? WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() : WC()->cart->get_subtotal();
			}

			//from 1.1.5
			if ( $exclude_coupons ) {
				$coupons = WC()->cart->get_applied_coupons();

				if ( $coupons ) {
					$coupons_total_amount = 0;
					foreach ( $coupons as $coupon ) {
						$coupons_total_amount += WC()->cart->get_coupon_discount_amount( $coupon, WC()->cart->display_cart_ex_tax );
					}
					$cart_total -= ( $coupons_total_amount );
				}
			}

			return $cart_total;
		}

		/**
		 * Is a valid message
		 *
		 * return a boolean if the message is valid or is expired
		 *
		 * @return   bool
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		function is_valid_user( $value, $message_id ) {
			if ( ! $value ) {
				return false;
			}
			$return    = false;
			$user_type = get_post_meta( $message_id, '_ywcm_message_user', true );
			$countries = get_post_meta( $message_id, '_ywcm_message_country', true );

			if ( ! empty( $countries ) ) {
				$location        = WC_Geolocation::geolocate_ip();
				$current_country = $location['country'];
				if ( ! in_array( $current_country, $countries ) ) {
					return false;
				}
			}

			if ( is_user_logged_in() && ( $user_type == 'customers' || $user_type == 'all' ) ) {
				$rules = (array) get_post_meta( $message_id, '_ywcm_message_role_user', true );

				if ( empty( $rules ) || in_array( 'all', $rules ) || in_array( '', $rules ) ) {
					return true;
				}

				$current_user = wp_get_current_user();
				$intersect    = array_intersect( $current_user->roles, $rules );
				if ( ! empty( $intersect ) ) {
					return true;
				}
			} else {
				if ( ( ! is_user_logged_in() && $user_type == 'guests' ) || $user_type == 'all' ) {
					return true;
				}
			}

			return $return;
		}

		/**
		 * Is a valid page
		 *
		 * return a boolean if the message is valid for this page
		 *
		 * @return   bool
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @since    1.0
		 */

		function is_valid_page( $value, $message_id ) {

			if ( ! $value ) {
				return false;
			}

			$is_valid_page = $value;
			$pages         = get_post_meta( $message_id, '_ywcm_message_pages', true );

			$show_on_current_page            = ! empty( $pages ) && in_array( $this->get_woocommerce_page(), $pages );
			$message_type                    = get_post_meta( $message_id, '_ywcm_message_type', true );
			$products_cart_show_only_in      = get_post_meta( $message_id, '_ywcm_products_cart_show_only_in', true );
			$simple_message_show_in_products = get_post_meta( $message_id, '_ywcm_simple_message_show_in_products', true );
			$show_on_current_page            = $show_on_current_page && ! $products_cart_show_only_in && ! $simple_message_show_in_products;


			if ( apply_filters( 'yith_ywcm_show_on_current_page', $show_on_current_page, $message_id, $pages ) ) {
				$is_valid_page = true;
			} else {
				$is_valid_page = false;
				if ( 'products_cart' == $message_type && is_singular() && $products_cart_show_only_in ) {
					global $product;
					if ( ! is_null( $product ) ) {
						$products_selected = (array) get_post_meta( $message_id, '_ywcm_products_cart_products', true );
						$products_excluded = (array) get_post_meta( $message_id, '_ywcm_products_cart_products_exclude', true );
						$products_selected = array_filter( $products_selected );
						$product_id        = $product->get_id();
						$is_valid_page     = empty( $products_selected ) || in_array( $product_id, $products_selected );
						if ( $products_selected && $is_valid_page ) {
							$is_valid_page = ! in_array( $product->get_id(), $products_excluded );
						}
					}
				}

				if ( 'simple_message' == $message_type && is_singular() ) {
					global $product;
					if ( ! is_null( $product ) ) {
						$simple_message_show_in_products = (array) get_post_meta( $message_id, '_ywcm_simple_message_show_in_products', true );

						$is_valid_page = in_array( $product->get_id(), $simple_message_show_in_products );
					}

				}
			}

			return apply_filters( 'yith_ywcm_is_valid_page', $is_valid_page, $message_id, $pages );
		}

		public function is_valid_extra_check( $value, $message_id ) {

			if ( ! $value ) {
				return false;
			}

			$is_valid     = $value;
			$message_type = get_post_meta( $message_id, '_ywcm_message_type', true );
			if ( 'simple_message' == $message_type ) {
				$show_in_products      = (array) get_post_meta( $message_id, '_ywcm_simple_message_show_in_products', true );
				$hide_products_in_cart = get_post_meta( $message_id, '_ywcm_simple_message_hide_products_in_cart', true );

				if ( $hide_products_in_cart && WC()->cart->get_cart() ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
						$product    = $values['data'];
						$product_id = $product->get_id();
						$parent_id  = yit_get_base_product_id( $product );

						if ( in_array( $product_id, $show_in_products ) || in_array( $parent_id, $show_in_products ) ) {
							$is_valid = false;
						}

					}
				}
			}

			return $is_valid;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_YWCM_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
				require_once( YITH_YWCM_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
			}
			YIT_Plugin_Licence()->register( YITH_YWCM_INIT, YITH_YWCM_SECRET_KEY, YITH_YWCM_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    2.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWCM_SLUG, YITH_YWCM_INIT );
		}

		/**
		 * get_woocommerce_page
		 *
		 * return the current page of wocommerce
		 *
		 * @return   Array
		 * @since    1.0
		 * @author   Emanuela Castorina
		 */
		function get_woocommerce_page() {

			if ( is_cart() ) {
				return 'cart';
			}

			if ( is_shop() ) {
				return 'shop';
			}

			if ( is_product() ) {
				return 'single-product';
			}

			if ( is_checkout() ) {
				return 'checkout';
			}

			return '';


		}


		/**
		 * @param $message_id
		 *
		 * @return mixed
		 */
		function get_message( $message_id, $is_ajax = false ) {
			foreach ( $this->messages as $key => $message ) {
				if ( ( $message->ID == $message_id && apply_filters( 'yith_ywcm_is_valid_message', $this->is_valid( $message->ID ), $message->ID ) )
				     || ( $message->ID == $message_id && $is_ajax ) ) {
					return $message;
				}
			}
		}


		/**
		 * @param $message
		 *
		 * @return string
		 */
		function call_message_template( $message ) {
			ob_start();
			$message_type = get_post_meta( $message->ID, '_ywcm_message_type', true );
			$layout       = ( get_post_meta( $message->ID, '_ywcm_message_layout', true ) !== '' ) ? get_post_meta( $message->ID, '_ywcm_message_layout', true ) : 'layout';
			$args         = ( method_exists( $this, 'get_' . $message_type . '_args' ) ) ? $this->{'get_' . $message_type . '_args'}( $message ) : false;
			if ( $args ) {
				$args['ywcm_id'] = $message->ID;
				yit_plugin_get_template( YITH_YWCM_DIR, '/layouts/' . $layout . '.php', $args );
			}

			return ob_get_clean();
		}
	}
}

