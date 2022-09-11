<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Helper Class.
 *
 * @class   YITH_WC_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Subscription_Helper' ) ) {

	/**
	 * Class YWSBS_Subscription_Helper
	 */
	class YWSBS_Subscription_Helper {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Helper
		 */
		protected static $instance;

		/**
		 * Variable to store the html prices bu product.
		 *
		 * @var array
		 */
		protected $change_price_list;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Helper
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_subscription_post_type' ) );
			add_action( 'ywsbs_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );

			// Add Capabilities to Administrator and Shop Manager.
			add_action( 'admin_init', array( $this, 'add_subscription_capabilities' ), 1 );

			// Change product price.
			add_filter( 'woocommerce_get_price_html', array( $this, 'change_price_html' ), 10, 2 );
			add_filter( 'woocommerce_variable_price_html', array( $this, 'change_variable_price_html' ), 10, 2 );

			add_action( 'ywsbs_updated_prop', array( $this, 'maybe_trigger_hook' ), 10, 4 );

		}

		/**
		 * Register ywsbs_subscription post type
		 *
		 * @since 1.0.0
		 */
		public function register_subscription_post_type() {

			$supports = false;

			if ( apply_filters( 'ywsbs_test_on', YITH_YWSBS_TEST_ON ) ) {
				$supports = array( 'custom-fields' );
			}

			$labels = array(
				'name'               => esc_html_x( 'Subscriptions', 'Post Type General Name', 'yith-woocommerce-subscription' ),
				'singular_name'      => esc_html_x( 'Subscription', 'Post Type Singular Name', 'yith-woocommerce-subscription' ),
				'menu_name'          => esc_html__( 'Subscription', 'yith-woocommerce-subscription' ),
				'parent_item_colon'  => esc_html__( 'Parent Item:', 'yith-woocommerce-subscription' ),
				'all_items'          => esc_html__( 'All Subscriptions', 'yith-woocommerce-subscription' ),
				'view_item'          => esc_html__( 'View Subscriptions', 'yith-woocommerce-subscription' ),
				'add_new_item'       => esc_html__( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'add_new'            => esc_html__( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'edit_item'          => esc_html__( 'Edit Subscription', 'yith-woocommerce-subscription' ),
				'update_item'        => esc_html__( 'Update Subscription', 'yith-woocommerce-subscription' ),
				'search_items'       => esc_html__( 'Search by Subscription ID', 'yith-woocommerce-subscription' ),
				'not_found'          => esc_html__( 'Not found', 'yith-woocommerce-subscription' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'yith-woocommerce-subscription' ),
			);

			$args = array(
				'label'               => esc_html__( 'ywsbs_subscription', 'yith-woocommerce-subscription' ),
				'labels'              => $labels,
				'supports'            => $supports,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'capability_type'     => 'ywsbs_sub',
				'capabilities'        => array(
					'read_post'          => 'read_ywsbs_sub',
					'read_private_posts' => 'read_ywsbs_sub',
					'edit_post'          => 'edit_ywsbs_sub',
					'edit_posts'         => 'edit_ywsbs_subs',
					'edit_others_post'   => 'edit_others_ywsbs_subs',
					'delete_post'        => 'delete_ywsbs_sub',
					'delete_others_post' => 'delete_others_ywsbs_subs',
				),
				'map_meta_cap'        => false,
			);

			register_post_type( YITH_YWSBS_POST_TYPE, $args );

			do_action( 'ywsbs_after_register_post_type' );

		}

		/**
		 * Flush rules if the event is queued.
		 *
		 * @since 2.0.0
		 */
		public static function maybe_flush_rewrite_rules() {
			if ( ! get_option( 'ywsbs_queue_flush_rewrite_rules' ) ) {
				update_option( 'ywsbs_queue_flush_rewrite_rules', 'yes' );
				flush_rewrite_rules();
			}
		}

		/**
		 * Return the list of subscription capabilities
		 *
		 * @return array
		 * @since  2.0.0
		 */
		public static function get_subscription_capabilities() {
			$caps = array(
				'read_post'          => 'read_ywsbs_sub',
				'read_others_post'   => 'read_others_ywsbs_subs',
				'edit_post'          => 'edit_ywsbs_sub',
				'edit_posts'         => 'edit_ywsbs_subs',
				'edit_others_post'   => 'edit_others_ywsbs_subs',
				'delete_post'        => 'delete_ywsbs_sub',
				'delete_others_post' => 'delete_others_ywsbs_subs',
			);

			return apply_filters( 'ywsbs_get_subscription_capabilities', $caps );
		}

		/**
		 * Add subscription management capabilities to Admin and Shop Manager
		 *
		 * @since 1.0.0
		 */
		public function add_subscription_capabilities() {

			// gets the admin and shop_manager roles.
			$admin               = get_role( 'administrator' );
			$enable_shop_manager = ( 'yes' === get_option( 'ywsbs_enable_shop_manager' ) );
			$shop_manager        = get_role( 'shop_manager' );

			foreach ( self::get_subscription_capabilities() as $key => $cap ) {
				$admin && $admin->add_cap( $cap );
				if ( $enable_shop_manager ) {
					$shop_manager && $shop_manager->add_cap( $cap );
				}
			}
		}

		/**
		 * Regenerate the capabilities.
		 *
		 * @since 2.0.0
		 */
		public static function maybe_regenerate_capabilities() {
			$shop_manager = get_role( 'shop_manager' );
			foreach ( self::get_subscription_capabilities() as $key => $cap ) {
				$shop_manager && $shop_manager->remove_cap( $cap );
			}
		}

		/**
		 * Return an array of pause options
		 *
		 * @param YWSBS_Subscription $subscription Subscription Object.
		 *
		 * @return array|bool
		 * @since  2.0.0
		 */
		public function get_subscription_product_pause_options( $subscription ) {

			$product_id = $subscription->get( 'variation_id' ) ? $subscription->get( 'variation_id' ) : $subscription->get( 'product_id' );

			return self::get_product_pause_info( $product_id );

		}

		/**
		 * Get the pause info of a subscription product.
		 *
		 * @param int|WC_Product $product Product.
		 *
		 * @return bool
		 * @since  2.0
		 */
		public static function get_product_pause_info( $product ) {

			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! $product ) {
				return false;
			}

			$enable_pause                   = $product->get_meta( '_ywsbs_enable_pause' );
			$_ywsbs_override_pause_settings = $product->get_meta( '_ywsbs_override_pause_settings' );
			$max_pause                      = $product->get_meta( '_ywsbs_max_pause' );
			$max_pause_duration             = $product->get_meta( '_ywsbs_max_pause_duration' );

			// porting from previous version.
			if ( empty( $_ywsbs_override_pause_settings ) ) {
				$_ywsbs_override_pause_settings = empty( $enable_pause ) ? 'no' : 'yes';
			}

			if ( 'yes' === $_ywsbs_override_pause_settings ) {

				// porting from previous version.
				if ( empty( $enable_pause ) ) {
					$enable_pause = empty( $max_pause ) ? 'no' : ( empty( $max_pause_duration ) ? 'yes' : 'limited' );
				}

				$product_pause_options['max_pause']          = ( 'limited' === $enable_pause ) ? $max_pause : 0;
				$product_pause_options['max_pause_duration'] = ( 'limited' === $enable_pause ) ? $max_pause_duration : 0;
				$product_pause_options['allow_pause']        = ( 'no' === $enable_pause ) ? 'no' : 'yes';
			} else {
				$max_pause          = get_option( 'ywsbs_max_pause', array( 'value' => 2 ) );
				$max_pause_duration = get_option( 'ywsbs_max_pause_duration', array( 'value' => 30 ) );
				$can_be_paused      = get_option( 'ywsbs_allow_users_to_pause_subscriptions', 'no' );

				$product_pause_options['max_pause']          = ( 'limited' === $can_be_paused ) ? $max_pause['value'] : 0;
				$product_pause_options['max_pause_duration'] = ( 'limited' === $can_be_paused ) ? $max_pause_duration['value'] : 0;
				$product_pause_options['allow_pause']        = ( 'no' === $can_be_paused ) ? 'no' : 'yes';
			}

			return $product_pause_options;

		}

		/**
		 * Calculate the gap payment in the upgrade processing.
		 *
		 * @param int                $variation_id Variation id.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return float
		 * @since  1.0.0
		 */
		public function calculate_gap_payment( $variation_id, $subscription ) {

			$activity_period = $this->get_activity_period( $subscription );
			$variation       = wc_get_product( $variation_id );
			$time_option     = $variation->get_meta( '_ywsbs_price_time_option' );
			$num_old_rates   = ceil( $activity_period / ywsbs_get_timestamp_from_option( 0, 1, $time_option ) );
			$var_price       = ( $variation->get_price() - ( $subscription->line_total + $subscription->line_tax ) ) * $num_old_rates;

			return ( $var_price > 0 ) ? $var_price : 0;
		}

		/**
		 * Return the timestamp from activation of subscription escluding pauses.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param bool               $exclude_pauses Exclude pauses from calculation.
		 *
		 * @return float|int
		 * @since  2.0.0
		 */
		public function get_activity_period( $subscription, $exclude_pauses = true ) {
			$timestamp = current_time( 'timestamp' ) - intval( $subscription->get( 'start_date' ) ); // phpcs:ignore
			if ( $exclude_pauses && $subscription->get( 'sum_of_pauses' ) ) {
				$timestamp -= $subscription->get( 'sum_of_pauses' );
			}

			return abs( $timestamp );
		}

		/**
		 * Calculate taxes.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return void|bool
		 * @since  2.0.0
		 */
		public function calculate_taxes( $subscription ) {
			$subtotal          = 0;
			$total             = 0;
			$shipping_tax      = 0;
			$subtotal_taxes    = array();
			$taxes             = array();
			$calculate_tax_for = $this->get_tax_location( $subscription );
			$product           = wc_get_product( $subscription->get( 'product_id' ) );
			$shipping_data     = $subscription->get( 'subscriptions_shippings' );

			if ( ! isset( $calculate_tax_for['country'], $calculate_tax_for['state'], $calculate_tax_for['postcode'], $calculate_tax_for['city'] ) ) {
				return false;
			}

			if ( wc_tax_enabled() ) {
				$tax_rates = WC_Tax::find_shipping_rates( $calculate_tax_for );
				$taxes     = WC_Tax::calc_tax( $subscription->get( 'order_shipping' ), $tax_rates, false );
				if ( ! empty( $shipping_data ) && is_array( $shipping_data ) ) {
					$shipping_data['taxes'] = $taxes;
				}
				$shipping_tax = $taxes ? array_sum( $taxes ) : 0;
			}

			if ( '0' !== $product->get_tax_class() && 'taxable' === $product->get_tax_status() && wc_tax_enabled() ) {
				$calculate_tax_for['tax_class'] = $product->get_tax_class();
				$tax_rates                      = WC_Tax::find_rates( $calculate_tax_for );
				$taxes                          = WC_Tax::calc_tax( $subscription->get( 'line_total' ), $tax_rates, false );
				$subtotal_taxes                 = WC_Tax::calc_tax( $subscription->get( 'line_subtotal' ), $tax_rates, false );
				$subtotal                       = $subtotal_taxes ? array_sum( $subtotal_taxes ) : 0;
				$total                          = $taxes ? array_sum( $taxes ) : 0;
			}

			$line_tax_data = array(
				'subtotal' => $subtotal_taxes,
				'total'    => $taxes,
			);

			$subscription->set( 'line_tax', $total );
			$subscription->set( 'order_tax', $total );
			$subscription->set( 'order_shipping_tax', $shipping_tax );
			$subscription->set( 'subscriptions_shippings', $shipping_data );
			$subscription->set( 'line_subtotal_tax', $subtotal );
			$subscription->set( 'line_tax_data', $line_tax_data );

		}

		/**
		 * Get tax location for this order.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param array              $args array Override the location.
		 *
		 * @return array
		 * @since  2.0.0
		 */
		public function get_tax_location( $subscription, $args = array() ) {
			$tax_based_on    = get_option( 'woocommerce_tax_based_on' );
			$shipping_fields = $subscription->get_address_fields( 'shipping' );
			$billing_fields  = $subscription->get_address_fields( 'billing' );

			if ( 'shipping' === $tax_based_on && ! $shipping_fields['shipping_country'] ) {
				$tax_based_on = 'billing';
			}

			$args = wp_parse_args(
				$args,
				array(
					'country'  => 'billing' === $tax_based_on ? $billing_fields['billing_country'] : $shipping_fields['shipping_country'],
					'state'    => 'billing' === $tax_based_on ? $billing_fields['billing_state'] : $shipping_fields['shipping_state'],
					'postcode' => 'billing' === $tax_based_on ? $billing_fields['billing_postcode'] : $shipping_fields['shipping_postcode'],
					'city'     => 'billing' === $tax_based_on ? $billing_fields['billing_city'] : $shipping_fields['shipping_city'],
				)
			);

			// Default to base.
			if ( 'base' === $tax_based_on || empty( $args['country'] ) ) {
				$default          = wc_get_base_location();
				$args['country']  = $default['country'];
				$args['state']    = $default['state'];
				$args['postcode'] = '';
				$args['city']     = '';
			}

			return $args;
		}

		/**
		 * Change the total amount meta on a subscription after a change without
		 * recalculate taxes.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @since 2.0.0
		 */
		public function calculate_totals_from_changes( $subscription ) {
			$changes = array();

			$changes['order_subtotal']     = floatval( $subscription->get( 'line_total' ) ) + floatval( $subscription->get( 'line_tax' ) );
			$changes['subscription_total'] = floatval( $subscription->get( 'order_shipping' ) ) + floatval( $subscription->get( 'order_shipping_tax' ) ) + $changes['order_subtotal'];
			$changes['order_total']        = $changes['subscription_total'];
			$changes['line_subtotal']      = round( floatval( $subscription->get( 'line_total' ) ) / $subscription->get( 'quantity' ), wc_get_price_decimals() );
			$changes['line_subtotal_tax']  = round( floatval( $subscription->get( 'line_tax' ) ) / $subscription->get( 'quantity' ), wc_get_price_decimals() );
			$changes['line_tax_data']      = array(
				'subtotal' => array( $changes['line_subtotal_tax'] ),
				'total'    => array( $subscription->get( 'line_tax' ) ),
			);

			$subscription->update_subscription_meta( $changes );
		}

		/**
		 * Get the next payment due date.
		 *
		 * If paused, calculate the next date for payment, checking.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return int
		 * @since  2.0.0
		 */
		public function get_payment_due_date_paused_offset( $subscription ) {
			if ( ! $subscription->has_status( 'paused' ) ) {
				return 0;
			}

			$date_pause = $subscription->get( 'date_of_pauses' );
			if ( empty( $date_pause ) ) {
				return 0;
			}

			$last   = array_pop( $date_pause );

			return current_time( 'timestamp' ) - $last;
		}

		/**
		 * Return the subscription recurring price formatted
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param string             $tax_display Display tax.
		 * @param bool               $show_time_option Show time option.
		 * @param bool               $shipping Add shipping price to total.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_formatted_recurring( $subscription, $tax_display = '', $show_time_option = true, $shipping = false ) {

			$price_time_option_string = ywsbs_get_price_per_string( $subscription->get( 'price_is_per' ), $subscription->get( 'price_time_option' ) );
			$tax_inc                  = get_option( 'woocommerce_prices_include_tax' ) === 'yes';

			if ( wc_tax_enabled() && ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) || $tax_inc ) ) {
				$shipping_price = $shipping ? $subscription->get_order_shipping() + $subscription->get_order_shipping_tax() : 0;
				$sbs_price      = $subscription->get_line_total() + $subscription->get_line_tax() + $shipping_price;
			} else {
				$shipping_price = $shipping ? $subscription->get_order_shipping() : 0;
				$sbs_price      = $subscription->get_line_total();
			}

			$recurring = wc_price( $sbs_price, array( 'currency' => $subscription->get( 'order_currency' ) ) );

			$recurring .= $show_time_option ? ' / ' . $price_time_option_string : '';

			$recurring = apply_filters_deprecated( 'ywsbs-recurring-price', array( $recurring, $subscription ), '2.0.0', 'ywsbs_recurring_price', 'This filter will be removed in the next major release' );

			return apply_filters( 'ywsbs_recurring_price', $recurring, $subscription );
		}

		/**
		 * Return the next payment due date if there are rates not payed
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function get_left_time_to_next_payment( $subscription ) {

			$left_time = 0;

			if ( $subscription->get( 'payment_due_date' ) ) {
				$left_time = $subscription->get( 'payment_due_date' ) - current_time( 'timestamp' ); // phpcs:ignore
			} elseif ( $subscription->get( 'expired_date' ) ) {
				$left_time = $subscription->get( 'expired_date' ) - current_time( 'timestamp' ); // phpcs:ignore
			}

			return $left_time;
		}

		/**
		 * Get all subscriptions of a user
		 *
		 * @param int $user_id User ID.
		 * @param int $page Page number.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function get_subscriptions_by_user( $user_id, $page = - 1 ) {

			$args = array(
				'post_type'  => YITH_YWSBS_POST_TYPE,
				'meta_key'   => 'user_id',  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => $user_id,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			);

			if ( - 1 === $page ) {
				$args['posts_per_page'] = - 1;
			} else {
				$args['posts_per_page'] = apply_filters( 'ywsbs_num_of_subscription_on_a_page_my_account', 10 );
				$args['paged']          = $page;
			}

			$subscriptions = get_posts( $args );

			return $subscriptions;
		}

		/**
		 * Check the option one time shippable of product.
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 */
		public static function is_one_time_shippable( $product ) {
			$main_product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
			$main_product    = wc_get_product( $main_product_id );

			return $main_product->get_meta( '_ywsbs_one_time_shipping' );
		}

		/**
		 * Return the subscription max_length of a product.
		 *
		 * @param WC_Product $product Product.
		 * @param bool|array $subscription_info Subscription information.
		 *
		 * @return string
		 */
		public static function get_total_subscription_price( $product, $subscription_info ) {

			$max_length = self::get_subscription_product_max_length( $product );

			if ( ! $max_length ) {
				return '';
			}

			$recurring_price = $subscription_info && isset( $subscription_info['recurring_price'] ) ? $subscription_info['recurring_price'] : $product->get_price();

			$total_price = $recurring_price * $max_length;

			if ( ! empty( $subscription_info['price_is_per'] ) ) {
				$total_price = $total_price / $subscription_info['price_is_per'];
			}

			if ( isset( $subscription_info, WC()->cart ) ) {
				$applied_coupons = WC()->cart->get_applied_coupons();
				$is_trial        = ( ! empty( $subscription_info['trial_per'] ) && $subscription_info['trial_per'] > 0 );

				if ( $applied_coupons ) {
					foreach ( $applied_coupons as $coupon_code ) {
						$coupon      = new WC_Coupon( $coupon_code );
						$coupon_type = $coupon->get_discount_type();
						$limited     = $coupon->get_meta( 'ywsbs_limited_for_payments' );

						$limit_is_valid = empty( $limited ) || $limited >= 1 || $is_trial || 0 == $product->get_price(); //phpcs:ignore
						$coupon_amount  = $coupon->get_amount();
						$valid          = ywsbs_coupon_is_valid( $coupon, WC()->cart, $product );
						if ( $valid && in_array( $coupon_type, array( 'recurring_percent', 'recurring_fixed' ), true ) && $limit_is_valid ) {
							$discount_amount = 0;
							switch ( $coupon_type ) {
								case 'recurring_percent':
									$discount_amount = round( ( $recurring_price / 100 ) * $coupon_amount, WC()->cart->dp );
									break;
								case 'recurring_fixed':
									$discount_amount = ( $recurring_price < $coupon_amount ) ? $recurring_price : $coupon_amount;
									break;
							}

							$total_price -= empty( $limited ) ? $discount_amount * $max_length : $discount_amount * $limited;
						}
					}
				}
			}

			$fee = $subscription_info && isset( $subscription_info['fee'] ) ? $subscription_info['fee'] : ywsbs_get_product_fee( $product );
			$fee = ! empty( $fee ) ? (float) $fee : 0;

			return apply_filters( 'ywsbs_get_total_subscription_price', $total_price + $fee, $product );

		}

		/**
		 * Return the subscription max_length of a product.
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 */
		public static function get_subscription_product_max_length( $product ) {

			$max_length        = $product->get_meta( '_ywsbs_max_length' );
			$enable_max_length = $product->get_meta( '_ywsbs_enable_max_length' );

			// previous version.
			if ( empty( $enable_max_length ) ) {
				return $max_length;
			}

			return ( 'yes' === $enable_max_length ) ? $max_length : '';
		}


		/**
		 * Get the formatted period for price
		 *
		 * @param WC_Product $product Product.
		 * @param array      $subscription_info List of subscription parameters.
		 *
		 * @return string
		 */
		public static function get_subscription_period_for_price( $product, $subscription_info = false ) {

			if ( ! $product ) {
				return '';
			}

			$price_is_per             = $subscription_info ? $subscription_info['price_is_per'] : $product->get_meta( '_ywsbs_price_is_per' );
			$price_time_option        = $subscription_info ? $subscription_info['price_time_option'] : $product->get_meta( '_ywsbs_price_time_option' );
			$price_time_option_string = ywsbs_get_price_per_string( $price_is_per, $price_time_option, false );

			// APPLY_FILTER: ywsbs_subscription_period_for_price: to filter the formatted subscription period for price.
			return apply_filters( 'ywsbs_subscription_period_for_price', $price_time_option_string, $product, $subscription_info );
		}

		/**
		 * Get the raw recurring price.
		 *
		 * @param WC_Product $product Product.
		 * @param array      $subscription_info List of subscription parameters.
		 *
		 * @return string
		 */
		public static function get_subscription_recurring_price( $product, $subscription_info = false ) {

			$recurring_price = $subscription_info && isset( $subscription_info['recurring_price'] ) ? $subscription_info['recurring_price'] : $product->get_price();

			if ( isset( $subscription_info, WC()->cart ) ) {
				$applied_coupons = WC()->cart->get_applied_coupons();
				$is_trial        = ( ! empty( $subscription_info['trial_per'] ) && $subscription_info['trial_per'] > 0 );

				if ( $applied_coupons ) {
					foreach ( $applied_coupons as $coupon_code ) {
						$coupon         = new WC_Coupon( $coupon_code );
						$coupon_type    = $coupon->get_discount_type();
						$limited        = $coupon->get_meta( 'ywsbs_limited_for_payments' );
						$limit_is_valid = empty( $limited ) || $limited > 1 || $is_trial || 0 == $product->get_price(); //phpcs:ignore
						$coupon_amount  = $coupon->get_amount();
						$valid          = ywsbs_coupon_is_valid( $coupon, WC()->cart, $product );
						if ( $valid && in_array( $coupon_type, array( 'recurring_percent', 'recurring_fixed' ), true ) && $limit_is_valid ) {
							$discount_amount = 0;
							switch ( $coupon_type ) {
								case 'recurring_percent':
									$discount_amount = round( ( $recurring_price / 100 ) * $coupon_amount, WC()->cart->dp );
									break;
								case 'recurring_fixed':
									$discount_amount = ( $recurring_price < $coupon_amount ) ? $recurring_price : $coupon_amount;
									break;
							}
							$recurring_price -= $discount_amount;
						}
					}
				}
			}

			// APPLY_FILTER: ywsbs_subscription_recurring_price: to filter raw recurring price.
			return apply_filters( 'ywsbs_subscription_recurring_price', $recurring_price, $product, $subscription_info );
		}


		/**
		 * Get the formatted period for price
		 *
		 * @param WC_Product $product Product.
		 * @param array      $subscription_info List of subscription parameters.
		 *
		 * @return string
		 */
		public static function get_subscription_max_length_formatted_for_price( $product, $subscription_info = false ) {

			$max_length = $subscription_info ? $subscription_info['max_length'] : self::get_subscription_product_max_length( $product );

			if ( empty( $max_length ) ) {
				return '';
			}

			$price_time_option    = $subscription_info ? $subscription_info['price_time_option'] : $product->get_meta( '_ywsbs_price_time_option' );
			$max_length_formatted = ywsbs_get_price_per_string( $max_length, $price_time_option, true );

			// APPLY_FILTER: ywsbs_subscription_max_length_formatted_for_price: to filter the formatted subscription period for price.
			return apply_filters( 'ywsbs_subscription_max_length_formatted_for_price', $max_length_formatted, $product );
		}

		/**
		 * Get the formatted fee price
		 *
		 * @param WC_Product    $product Product.
		 * @param int           $qty Quantity.
		 * @param WC_Order|null $order Order.
		 * @param array         $subscription_info List of subscription parameters.
		 *
		 * @return string
		 */
		public static function get_fee_price( $product, $qty = 1, $order = null, $subscription_info = false ) {

			$show_fee   = 'yes' === get_option( 'ywsbs_show_fee' );
			$signup_fee = $subscription_info && isset( $subscription_info['fee'] ) ? $subscription_info['fee'] : ywsbs_get_product_fee( $product, 'edit' );
			$signup_fee = $show_fee ? $signup_fee : false;
			$fee_price  = '';

			$switching = isset( $subscription_info['switching'] ) ? $subscription_info['switching'] : 0;
			if ( $switching ) {
				$signup_fee = $subscription_info['fee'] - $subscription_info['recurring_price'];
			}

			$signup_fee = apply_filters( 'ywsbs_product_fee', $signup_fee, $product );

			if ( $show_fee && $signup_fee && $signup_fee > 0 ) {
				$currency   = ! is_null( $order ) ? $order->get_currency() : get_woocommerce_currency();
				$fee_format = get_option( 'ywsbs_show_fee_text', esc_html_x( '+ a signup fee of {{feeprice}}', 'do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) );
				$fee_price  = wc_get_price_to_display(
					$product,
					array(
						'qty'   => $qty,
						'price' => $signup_fee,
					)
				);
				$fee_price  = wc_price( $fee_price, array( 'currency' => $currency ) );
				$fee_price  = ' ' . str_replace( '{{feeprice}}', $fee_price, $fee_format );
			}

			// APPLY_FILTER: ywsbs_fee_price: to filter the trial price.
			return apply_filters( 'ywsbs_fee_price', $fee_price, $product, $order, $subscription_info );

		}

		/**
		 * Get the formatted trial price
		 *
		 * @param WC_Product $product Product.
		 * @param string     $page Page where the price will be shown.
		 * @param array      $subscription_info List of subscription parameters.
		 *
		 * @return string
		 */
		public static function get_trial_price( $product, $page = 'product', $subscription_info = false ) {

			$show_trial   = ( 'product' === $page ) ? ( 'yes' === get_option( 'ywsbs_show_trial_period' ) ) : true;
			$trial_period = ( $subscription_info && isset( $subscription_info['trial_per'] ) ) ? $subscription_info['trial_per'] : ywsbs_get_product_trial( $product );
			$trial_period = $show_trial ? apply_filters( 'ywsbs_change_trial_period', $trial_period ) : false;
			$trial_price  = '';

			if ( $show_trial && $trial_period ) {
				$trial_time_option = $subscription_info && isset( $subscription_info['trial_time_option'] ) ? $subscription_info['trial_time_option'] : $product->get_meta( '_ywsbs_trial_time_option' );
				if ( 'product' === $page ) {
					$trial_format = get_option( 'ywsbs_show_trial_period_text', esc_html_x( 'Get a {{trialtime}} free trial!', 'do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) );
				} else {
					$trial_format = get_option( 'ywsbs_show_trial_period_text_on_cart', esc_html_x( 'and {{trialtime}} free trial', 'do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) );
				}

				$trial_time  = ywsbs_get_price_per_string( $trial_period, $trial_time_option, true );
				$trial_price = ' ' . str_replace( '{{trialtime}}', $trial_time, $trial_format );
			}

			// APPLY_FILTER: ywsbs_trial_price: to filter the trial price.
			return apply_filters( 'ywsbs_trial_price', $trial_price, $product, $subscription_info );
		}


		/**
		 * Get next billing payment date
		 *
		 * @param WC_Product $product Subscription product.
		 * @param int        $start_date Start date in timestamp.
		 *
		 * @return int
		 */
		public static function get_billing_payment_due_date( $product, $start_date = 0 ) {

			$trial_per         = (int) ywsbs_get_product_trial( $product );
			$trial_time_option = $product->get_meta( '_ywsbs_trial_time_option' );
			$trial_period      = ywsbs_get_timestamp_from_option( 0, $trial_per, $trial_time_option );

			$price_is_per      = (int) $product->get_meta( '_ywsbs_price_is_per' );
			$price_time_option = $product->get_meta( '_ywsbs_price_time_option' );

			$start_date = ! empty( $start_date ) ? $start_date : current_time( 'timestamp' ); // phpcs:ignore

			if ( 0 !== $trial_period ) {
				$timestamp = $start_date + $trial_period;
			} else {
				$timestamp = ywsbs_get_timestamp_from_option( $start_date, $price_is_per, $price_time_option );
			}

			return $timestamp;
		}

		/**
		 * Change the price format to a subscription product.
		 *
		 * This method is called by an add filter to 'woocommerce_get_price_html'.
		 * but it can be called in different part of plugin.
		 *
		 * @param string        $price Price.
		 * @param WC_Product    $product Product.
		 * @param null|WC_Order $order Order.
		 * @param int           $qty Quantity.
		 *
		 * @return string
		 */
		public function change_price_html( $price, $product, $order = null, $qty = 1 ) {

			if ( isset( $this->change_price_list[ $product->get_id() ] ) ) {
				return $this->change_price_list[ $product->get_id() ];
			}

			// APPLY_FILTER: ywsbs_skip_price_html_filter: return true it is possible skip the change price html.
			if ( ! ywsbs_is_subscription_product( $product ) || apply_filters( 'ywsbs_skip_price_html_filter', false, $product, $order ) ) {
				return apply_filters( 'ywsbs_skipped_price_html_filter', $price );
			}

			$price .= '<span class="price_time_opt"> / ' . self::get_subscription_period_for_price( $product ) . '</span>';

			$trial_type  = is_admin() ? 'checkout' : 'product';
			$max_length  = self::get_subscription_max_length_formatted_for_price( $product );
			$trial_price = self::get_trial_price( $product, $trial_type );

			$fee_price        = self::get_fee_price( $product, $qty, $order );
			$sync_message     = YWSBS_Subscription_Synchronization::get_instance()->get_product_sync_message( $product );
			$delivery_message = YWSBS_Subscription_Delivery_Schedules()->get_product_delivery_message( $product );
			$show_details     = ! empty( $trial_price ) || ! empty( $fee_price ) || ! empty( $sync_message ) || ! empty( $delivery_message );

			if ( ! $product->is_type( 'variable' ) && ( ! empty( $max_length ) || $show_details ) ) {

				if ( ! empty( $max_length ) && apply_filters( 'ywsbs_show_max_length', true, $max_length ) ) {
					$price .= esc_html__( ' for ', 'yith-woocommerce-subscription' ) . $max_length;
				}

				$price .= $show_details ? '<span class="ywsbs-price-detail">' : '';

				if ( ! empty( $fee_price ) ) {
					$price .= '<span class="ywsbs-signup-fee">';
					$price .= $fee_price;
					$price .= '</span>';
				}

				if ( ! is_admin() && ! empty( $sync_message ) ) {
					$hide_in_loop = apply_filters( 'ywsbs_hide_in_loop_sync_info', true, $product );
					if ( is_single() || ! $hide_in_loop ) {
						$price .= '<span class="ywsbs-synch-info">';
						$price .= $sync_message;
						$price .= '</span>';
						$price  = apply_filters( 'ywsbs_single_sync_message', $price, $sync_message, $product );
					}
				}

				if ( ! is_admin() && ! empty( $delivery_message ) ) {
					$hide_in_loop = apply_filters( 'ywsbs_hide_in_delivery_sync_info', true, $product );
					if ( is_single() || ! $hide_in_loop ) {
						$price .= '<span class="ywsbs-delivery-info">';
						$price .= $delivery_message;
						$price .= '</span>';
					}
				}

				if ( ! empty( $trial_price ) ) {
					$price .= '<span class="ywsbs-trial-period">';
					$price .= $trial_price;
					$price .= '</span>';
				}

				$price .= $show_details ? '</span>' : '';
			}

			$price = apply_filters_deprecated( 'ywsbs_change_price_html', array( $price, $product, $product->get_meta( '_ywsbs_price_is_per' ), $product->get_meta( '_ywsbs_price_time_option' ), $product->get_meta( '_ywsbs_max_length' ), ywsbs_get_product_fee( $product ), ywsbs_get_product_trial( $product ) ), '2.0.0', 'ywsbs_change_product_price_html', 'This filter will be removed in next major release' );

			$price = apply_filters( 'ywsbs_change_product_price_html', $price, $product );

			$this->change_price_list[ $product->get_id() ] = $price;

			// APPLY_FILTER: ywsbs_change_product_price_html: to change the html price of a subscription product.
			return $price;

		}


		/**
		 * Change the html price to a variation product
		 *
		 * @param string     $price Product price.
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 */
		public function change_variable_price_html( $price, $product ) {

			$variations = $product->get_available_variations();

			$has_subscriptions = false;
			foreach ( $variations as $variation ) {
				if ( ywsbs_is_subscription_product( $variation['variation_id'] ) ) {
					$has_subscriptions = true;
					break;
				}
			}

			// APPLY_FILTER: ywsbs_skip_price_html_filter: return true it is possible skip the change price html.
			if ( ! $has_subscriptions && apply_filters( 'ywsbs_skip_price_html_filter_on_variation', false, $product ) ) {
				return apply_filters( 'ywsbs_skipped_price_html_filter', $price );
			}

			$prices             = $product->get_variation_prices( true );
			$variations_ordered = array_keys( $prices['price'] );

			$min_price    = current( $prices['price'] );
			$min_var      = current( $variations_ordered );
			$min_var_prod = wc_get_product( $min_var );
			$max_price    = end( $prices['price'] );
			$max_var      = end( $variations_ordered );
			$max_var_prod = wc_get_product( $max_var );

			$min_reg_price = current( $prices['regular_price'] );
			$max_reg_price = end( $prices['regular_price'] );

			$min_period = self::get_subscription_period_for_price( $min_var_prod );
			$max_period = self::get_subscription_period_for_price( $max_var_prod );
			if ( $min_price !== $max_price ) {

				if ( ywsbs_is_subscription_product( $min_var_prod ) ) {
					$price = ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . ( ( $min_period !== $max_period ) ? '<span class="price_time_opt"> / ' . self::get_subscription_period_for_price( $min_var_prod ) . '</span>' : '' );
				} else {
					$price = is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price;
				};

				$price .= ' &ndash; ';

				if ( ywsbs_is_subscription_product( $max_var_prod ) ) {
					$price .= ( is_numeric( $max_price ) ? wc_price( $max_price ) : $max_price ) . '<span class="price_time_opt"> / ' . self::get_subscription_period_for_price( $max_var_prod ) . '</span>';
				} else {
					$price .= is_numeric( $max_price ) ? wc_price( $max_price ) : $max_price;
				}
			} elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
				if ( ywsbs_is_subscription_product( $min_var_prod ) ) {
					$min_html_price = ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . '<span class="price_time_opt"> / ' . self::get_subscription_period_for_price( $min_var_prod ) . '</span>';
				} else {
					$min_html_price = is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price;
				};
				$price = '<del>' . ( is_numeric( $max_reg_price ) ? wc_price( $max_reg_price ) : $max_reg_price ) . '</del> <ins>' . $min_html_price . '</ins>';
			} else {
				if ( ywsbs_is_subscription_product( $min_var_prod ) ) {
					$price = ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . '<span class="price_time_opt"> / ' . self::get_subscription_period_for_price( $min_var_prod ) . '</span>';
				} else {
					$price = is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price;
				};
			}

			return apply_filters( 'ywsbs_change_variation_product_price_html', $price, $product );

		}

		/**
		 * Return the total price of a subscription.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return string
		 */
		public static function get_total_subscription_amount( $subscription ) {

			$sbs_total_format = get_option( 'ywsbs_total_subscription_length_text', esc_html_x( 'Subscription total for {{sub-time}}: {{sub-total}}', 'do not translate the text inside the brackets', 'yith-woocommerce-subscription' ) );
			$max_length_text  = ywsbs_get_price_per_string( $subscription->get( 'max_length' ), $subscription->get( 'price_time_option' ), true );

			$fee                      = $subscription->get( 'fee' ) ? (int) $subscription->get( 'fee' ) : 0;
			$total_subscription_price = ( $subscription->get( 'subscription_total' ) / $subscription->get( 'price_is_per' ) ) * $subscription->get( 'max_length' ) + $fee;
			$total_subscription_price = wc_price( $total_subscription_price, array( 'currency' => $subscription->get_order_currency() ) );

			$sbs_total_format = str_replace( '{{sub-time}}', $max_length_text, $sbs_total_format );
			$sbs_total_format = str_replace( '{{sub-total}}', $total_subscription_price, $sbs_total_format );
			$sbs_total_format = '<div class="ywsbs-subscription-total">' . $sbs_total_format . '<div>';

			return $sbs_total_format;
		}

		/**
		 * If is necessary will be triggered some actions.
		 *
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param string             $prop Properties updated.
		 * @param mixed              $value Value of prop.
		 * @param mixed              $old_value Old value of prop.
		 */
		public function maybe_trigger_hook( $subscription, $prop, $value, $old_value ) {
			$date_changes = array(
				'start_date',
				'payment_due_date',
				'expired_date',
				'expired_pause_date',
				'next_attempt_date',
				'next_failed_status_change_date',
				'end_date',
				'check_the_renew_order',
			);

			if ( in_array( $prop, $date_changes, true ) ) {
				do_action( 'ywsbs_updated_subscription_date', $subscription, $prop, $value, $old_value );
			}
		}
	}

}


/**
 * Unique access to instance of YWSBS_Subscription class
 *
 * @return YWSBS_Subscription_Helper
 */
function YWSBS_Subscription_Helper() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YWSBS_Subscription_Helper::get_instance();
}
