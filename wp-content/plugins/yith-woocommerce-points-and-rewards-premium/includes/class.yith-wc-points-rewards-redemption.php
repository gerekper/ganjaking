<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards
 *
 * @class   YITH_WC_Points_Rewards_Redemption
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Redemption' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Redemption
	 */
	class YITH_WC_Points_Rewards_Redemption {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Redemption
		 */
		protected static $instance;

		/**
		 * @var string
		 */
		protected $label_coupon_prefix = 'ywpar_discount';

		/**
		 * @var string
		 */
		protected $coupon_type = 'fixed_cart';
		/**
		 * @var string
		 */
		protected $current_coupon_code = '';

		/**
		 * @var int
		 */
		protected $max_points = 0;

		/**
		 * @var int
		 */
		protected $max_discount = 0;

		/**
		 * @var int
		 */
		protected $max_percentual_discount = 0;

		/**
		 * @var array
		 */
		protected $args = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Redemption
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
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			if ( ! YITH_WC_Points_Rewards()->is_enabled() ) {
				return;
			}

			// register the coupon and the point used at checkout
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_order_meta' ), 10 );

			// remove points if are used in order
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'deduce_order_points' ), 20 );
			add_action( 'woocommerce_order_status_failed', array( $this, 'remove_redeemed_order_points' ) );
			add_action( 'woocommerce_removed_coupon', array( $this, 'clear_current_coupon' ) );

			add_action(
				'woocommerce_order_status_changed',
				array(
					$this,
					'clear_ywpar_coupon_after_create_order',
				),
				10,
				2
			);

			add_action( 'wp_loaded', array( $this, 'apply_discount' ), 30 );

			add_action( 'woocommerce_cart_item_removed', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_discount' ) );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'update_discount' ), 99 );

			add_action( 'init', array( $this, 'init' ) );

			if ( is_user_logged_in() ) {
				add_filter( 'woocommerce_coupon_message', array( $this, 'coupon_rewards_message' ), 15, 3 );
				add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ), 10, 2 );
			}

			add_action( 'wp_loaded', array( $this, 'ywpar_set_cron' ) );
			add_action( 'ywpar_clean_cron', array( $this, 'clear_coupons' ) );

			// auto-apply points in cart/checkout pages
			if ( YITH_WC_Points_Rewards()->get_option( 'enable_rewards_points', 'no' ) == 'yes' && YITH_WC_Points_Rewards()->get_option( 'autoapply_points_cart_checkout', 'no' ) == 'yes' ) {
				add_action( 'template_redirect', array( $this, 'auto_apply_discount' ), 30 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'clean_auto_apply_session' ) );
				add_action( 'woocommerce_check_cart_items', array( $this, 'clean_auto_apply_session' ) );
			}
		}

		public function init() {
			// remove point when the order is cancelled
			if ( YITH_WC_Points_Rewards()->get_option( 'remove_point_order_deleted' ) == 'yes' ) {
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'remove_redeemed_order_points' ) );
				add_action(
					'woocommerce_order_status_cancelled_to_completed',
					array(
						$this,
						'add_redeemed_order_points',
					)
				);
				add_action(
					'woocommerce_order_status_cancelled_to_processing',
					array(
						$this,
						'add_redeemed_order_points',
					)
				);
				add_action(
					'woocommerce_order_status_failed_to_completed',
					array(
						$this,
						'add_redeemed_order_points',
					)
				);
			}

			// remove point when the order is refunded
			if ( YITH_WC_Points_Rewards()->get_option( 'reassing_redeemed_points_refund_order' ) == 'yes' ) {
				add_action(
					'woocommerce_order_partially_refunded',
					array(
						$this,
						'remove_redeemed_order_points',
					),
					11,
					2
				);
				add_action( 'woocommerce_order_fully_refunded', array( $this, 'remove_redeemed_order_points' ), 11, 2 );
				add_action(
					'wp_ajax_nopriv_woocommerce_delete_refund',
					array(
						$this,
						'add_redeemed_order_points',
					),
					9,
					2
				);
				add_action( 'wp_ajax_woocommerce_delete_refund', array( $this, 'add_redeemed_order_points' ), 9, 2 );
			}
		}


		/**
		 * Remove the coupons after that the order is created
		 *
		 * @param $order WC_Order
		 *
		 * @param $status_from
		 *
		 * @return void
		 */
		public function clear_ywpar_coupon_after_create_order( $order, $status_from ) {
			if ( $status_from != 'pending' ) {
				return;
			}
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( version_compare( WC()->version, '3.7.0', '<' ) ) {
				$coupon_used = $order->get_used_coupons();
			} else {
				$coupon_used = $order->get_coupon_codes();
			}

			if ( $coupon_used ) {
				foreach ( $coupon_used as $coupons_code ) {
					$coupon = new WC_Coupon( $coupons_code );
					if ( $this->check_coupon_is_ywpar( $coupon ) ) {
						$coupon->delete();
					}
				}
			}
		}

		/**
		 * Remove the coupons created dinamically
		 *
		 * @param $coupon_code string The coupon code removed
		 *
		 * @return void
		 */
		public function clear_current_coupon( $coupon_code ) {
			$current_coupon = $this->get_current_coupon();
			if ( $current_coupon instanceof WC_Coupon && $current_coupon->get_code() == $coupon_code ) {
				$current_coupon->delete();
			}
		}

		/**
		 * Add the redeemed points when an order is cancelled
		 * *
		 *
		 * @param $order_id
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function remove_redeemed_order_points( $order_id ) {
			$order                      = wc_get_order( $order_id );
			$redemped_points            = yit_get_prop( $order, '_ywpar_redemped_points', true );
			$redemped_points_reassigned = yit_get_prop( $order, '_ywpar_redemped_points_reassigned', true );
			$discount_amount            = yit_get_prop( $order, '_ywpar_coupon_amount' );

			if ( '' === $redemped_points || $redemped_points_reassigned != '' ) {
				return;
			}

			$customer_user = $order->get_customer_id();
			$points        = $redemped_points;
			// $action        = ( current_action() == 'woocommerce_order_fully_refunded' ) ? 'order_refund' : 'order_' . $order->get_status();
			$action = 'order_refund';
			if ( $customer_user ) {
				$current_discount_total_amount = get_user_meta( $customer_user, '_ywpar_user_total_discount', true );
				update_user_meta( $customer_user, '_ywpar_user_total_discount', $current_discount_total_amount - $discount_amount );

				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, $points, $action, $order_id );
				$this->set_user_rewarded_points( $customer_user, - $points );
				$order->add_order_note( sprintf( _x( 'Added %1$d %2$s for order %3$s.', 'First placeholder: number of points; second placeholder: label of points; third placeholder: order number', 'yith-woocommerce-points-and-rewards' ), $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ), YITH_WC_Points_Rewards()->get_action_label( $action ) ), 0 );

				yit_save_prop( $order, '_ywpar_redemped_points_reassigned', $points );
			}
		}

		/**
		 * Removed the redeemed points when an order changes status from cancelled to complete
		 * *
		 *
		 * @param $order_id
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function add_redeemed_order_points( $order_id ) {
			$order                      = wc_get_order( $order_id );
			$redemped_points            = yit_get_prop( $order, '_ywpar_redemped_points', true );
			$redemped_points_reassigned = yit_get_prop( $order, '_ywpar_redemped_points_reassigned', true );
			if ( $redemped_points == '' || $redemped_points_reassigned == '' ) {
				return;
			}

			$customer_user = method_exists( $order, 'get_customer_id' ) ? $order->get_customer_id() : yit_get_prop( $order, '_customer_user', true );
			$points        = $redemped_points;
			$action        = 'order_' . $order->get_status();

			if ( $customer_user > 0 ) {
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, - $points, $action, $order_id );
				$order->add_order_note( sprintf( _x( 'Removed %1$d %2$s for order %3$s.','First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), - $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ), YITH_WC_Points_Rewards()->get_action_label( $action ) ), 0 );
				yit_save_prop( $order, '_ywpar_redemped_points_reassigned', '' );
			}
		}

		/**
		 * Apply the discount to cart after that the user set the number of points
		 * *
		 *
		 * @return void
		 * @throws WC_Data_Exception
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function apply_discount() {

			if ( YITH_WC_Points_Rewards()->get_option( 'enable_rewards_points' ) != 'yes' ) {
				return;
			}

			if ( wp_verify_nonce( 'ywpar_input_points_nonce', 'ywpar_apply_discounts' ) || ! is_user_logged_in() || ! isset( $_POST['ywpar_rate_method'] ) || ! isset( $_POST['ywpar_points_max'] ) || ! isset( $_POST['ywpar_max_discount'] ) || ( isset( $_POST['coupon_code'] ) && $_POST['coupon_code'] != '' ) ) {
				return;
			}
			$posted = $_POST;
			$this->apply_discount_calculation( $posted );
		}


		/**
		 * Auto Apply Reediming Points in cart/checkout pages
		 *
		 * @return void
		 * @throws WC_Data_Exception
		 * @author Armando Liccardo
		 * @since  1.6.7
		 */
		public function auto_apply_discount() {

			if ( ! is_cart() && ! is_checkout() ) {
				return; }

				/*
				 clean the session ywpar_automatically_applied value if more than one hour has passed
			this is like a session clean for auto apply discount value */
			$prev = WC()->session->get( 'ywpar_automatically_applied_time' );
			if ( isset( $prev ) && ! empty( $prev ) ) {
				$now      = new DateTime();
				$interval = $prev->diff( $now );
				if ( intval( $interval->i ) >= apply_filters( 'ywpar_autoapply_clean_time_interval', 60 ) ) {
					WC()->session->set( 'ywpar_automatically_applied', false );
				}
			}

			$ywpar_automatically_applied = WC()->session->get( 'ywpar_automatically_applied' );

			if ( ! $ywpar_automatically_applied || ! isset( $ywpar_automatically_applied ) || empty( $ywpar_automatically_applied ) ) {
				$values = array();

				$values['ywpar_rate_method']  = YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' );
				$values['ywpar_max_discount'] = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
				$values['ywpar_points_max']   = YITH_WC_Points_Rewards_Redemption()->get_max_points();

				$values['ywpar_input_points']       = $values['ywpar_points_max'];
				$values['ywpar_input_points_check'] = 1;

				$this->apply_discount_calculation( $values );

				WC()->session->set( 'ywpar_automatically_applied', true );
				$d = new DateTime();
				WC()->session->set( 'ywpar_automatically_applied_time', $d );
			}

		}

		/**
		 * Clean Auto Apply Reediming Points session info in order to re-apply after checkout completed and order has set to completed or cart emptied
		 *
		 * @param string $order
		 * @return void
		 * @since  1.6.7
		 * @author Armando Liccardo
		 */
		public function clean_auto_apply_session( $order = '' ) {
			if ( $order ) {
				WC()->session->set( 'ywpar_automatically_applied', false );
			} else {
				if ( WC()->cart->get_cart_contents_count() == 0 ) {
					WC()->session->set( 'ywpar_automatically_applied', false );
				}
			}

		}

		/**
		 * @param      $posted
		 * @param bool   $apply_coupon
		 *
		 * @throws Exception
		 * @throws WC_Data_Exception
		 */
		public function apply_discount_calculation( $posted, $apply_coupon = true ) {

			do_action( 'ywpar_before_apply_discount_calculation' );
			$max_points   = $posted['ywpar_points_max'];
			$max_discount = $posted['ywpar_max_discount'];
			$coupon_label = $this->get_coupon_code_prefix();
			$discount     = 0;

			if ( $posted['ywpar_rate_method'] == 'fixed' ) {

				if ( ! isset( $posted['ywpar_input_points_check'] ) || $posted['ywpar_input_points_check'] == 0 ) {
					return;
				}

				$input_points = $posted['ywpar_input_points'];

				if ( $input_points == 0 ) {
					return;
				}

				$input_points       = ( $input_points > $max_points ) ? $max_points : $input_points;
				$conversion         = $this->get_conversion_rate_rewards();
				$input_max_discount = $input_points / $conversion['points'] * $conversion['money'];
				// check that is not lg than $max discount
				$input_max_discount      = ( $input_max_discount > $max_discount ) ? $max_discount : $input_max_discount;
				$minimum_discount_amount = YITH_WC_Points_Rewards()->get_option( 'minimum_amount_discount_to_redeem' );

				if ( ! empty( $minimum_discount_amount ) && $input_max_discount < $minimum_discount_amount ) {
					$input_max_discount = $minimum_discount_amount;
					$input_points       = $conversion['points'] / $conversion['money'] * $input_max_discount;
				}

				if ( $input_max_discount > 0 ) {
					WC()->session->set( 'ywpar_coupon_code_points', $input_points );
					WC()->session->set( 'ywpar_coupon_code_discount', $input_max_discount );
					$discount = $input_max_discount;
					$discount = apply_filters( 'ywpar_adjust_discount_value', $discount );

				};

			} elseif ( $posted['ywpar_rate_method'] == 'percentage' ) {
				WC()->session->set( 'ywpar_coupon_code_points', $max_points );
				WC()->session->set( 'ywpar_coupon_code_discount', $max_discount );
				$discount = $max_discount;
			}

			WC()->session->set( 'ywpar_coupon_posted', $posted );

			// apply the coupon in cart
			if ( $apply_coupon && $discount ) {

				$coupon = $this->get_current_coupon();
				$is_new = $coupon->get_amount() === '0';
				if ( apply_filters( 'ywpar_change_coupon_type_discount', false, $discount, $coupon ) ) {
					$type_discount = 'percentage';
					$discount      = '100';

				} else {
					$type_discount = 'fixed_cart';
				}

				if ( $coupon->get_discount_type() !== $type_discount ) {
					$coupon->set_discount_type( $type_discount );
				}

				if ( $coupon->get_amount() !== $discount ) {
					$coupon->set_amount( $discount );
				}
				$allow_free_shipping = apply_filters( 'ywpar_allow_free_shipping', YITH_WC_Points_Rewards()->get_option( 'allow_free_shipping_to_redeem', 'no' ) == 'yes', $discount );

				if ( $coupon->get_free_shipping() !== $allow_free_shipping ) {
					$coupon->set_free_shipping( $allow_free_shipping );
				}

				$valid = ywpar_coupon_is_valid( $coupon, WC()->cart );

				if ( ! $valid ) {
					$args = array(
						'id'             => false,
						'discount_type'  => $type_discount,
						'individual_use' => false,
						'usage_limit'    => $this->get_usage_limit(),
					);

					$coupon->add_meta_data( 'ywpar_coupon', 1 );
					$coupon->read_manual_coupon( $coupon->get_code(), $args );

				}

				if ( $is_new || ! empty( $coupon->get_changes() ) ) {
					$coupon->save();
				}

				$coupon_label = $coupon->get_code();

				if ( ywpar_coupon_is_valid( $coupon, WC()->cart ) && ! WC()->cart->has_discount( $coupon_label ) ) {
					WC()->cart->add_discount( $coupon_label );
					$this->update_discount();
				}
			}
		}

		/**
		 * Update the coupon code points and discount
		 *
		 * @return void
		 * @throws WC_Data_Exception
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		public function update_discount() {

			$applied_coupons = WC()->cart->get_applied_coupons();

			if ( $coupon = $this->check_coupon_is_ywpar( $applied_coupons ) ) {

				if ( YITH_WC_Points_Rewards()->get_option( 'enable_rewards_points' ) != 'yes' ) {
					WC()->cart->remove_coupon( $coupon->get_code() );

					return;
				}

				$posted               = WC()->session->get( 'ywpar_coupon_posted' );
				$coupon_real_discount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), false );
				$max_discount         = $this->calculate_rewards_discount( $coupon_real_discount );

				$posted               = WC()->session->get( 'ywpar_coupon_posted' );
				$ex_tax               = apply_filters( 'ywpar_exclude_taxes_from_calculation', false );
				$coupon_real_discount = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), $ex_tax );
				$max_discount         = $this->calculate_rewards_discount( $coupon_real_discount );

				if ( $max_discount ) {
					// minimum subtotal cart requested to redeeem points
					$minimum_amount             = YITH_WC_Points_Rewards()->get_option( 'minimum_amount_to_redeem' );
					$minimum_discount_requested = floatval( YITH_WC_Points_Rewards()->get_option( 'minimum_amount_discount_to_redeem' ) );

					if ( apply_filters( 'ywpar_exclude_taxes_from_calculation', false ) ) {
						$subtotal = (float) WC()->cart->get_subtotal();
					} else {
						$subtotal = ( (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax() );
					}

					if ( ( $minimum_amount !== '' && $minimum_amount > $subtotal ) || ( $minimum_discount_requested !== '' && $minimum_discount_requested > $max_discount ) ) {
						WC()->cart->remove_coupon( $coupon->get_code() );
					} else {

						$max_points                   = $this->get_max_points();
						$posted['ywpar_max_discount'] = $max_discount;
						$posted['ywpar_points_max']   = $max_points;
						// todo:add undo action
						$apply_coupon = true;// ( current_filter() == 'woocommerce_cart_item_removed' );
						$this->apply_discount_calculation( $posted, $apply_coupon );
					}
				} else {

					WC()->cart->remove_coupon( $coupon->get_code() );
				}
			}
		}

		/**
		 * Return the coupon code
		 *
		 * @return string
		 * @author Emanuela Castorina
		 * @since  1.0.0
		 */
		public function get_coupon_code_prefix() {
			return apply_filters( 'ywpar_label_coupon', $this->label_coupon_prefix );
		}

		/**
		 * Return the coupon code
		 * This method is @return string
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 * @deprecated from YITH Points and Rewards 1.2.0. Use 'get_coupon_code_prefix' instead.
		 */
		public function get_coupon_code() {
			return $this->get_coupon_code_prefix();
		}

		/**
		 * Return the coupon code attributes
		 *
		 * @param $args
		 * @param $code
		 *
		 * @return array
		 * @author Emanuela Castorina
		 *
		 * @since  1.0.0
		 */
		function create_coupon_discount( $args, $code ) {

			if ( $code == $this->get_coupon_code_prefix() ) {

				$this->args = array(
					'amount'           => $this->get_discount_amount(),
					'coupon_amount'    => $this->get_discount_amount(), // 2.2
					'apply_before_tax' => 'yes',
					'type'             => $this->coupon_type,
					'free_shipping'    => YITH_WC_Points_Rewards()->get_option( 'allow_free_shipping_to_redeem', 'no' ),
					'individual_use'   => 'no',
				);

				return $this->args;

			}

			return $args;
		}

		/**
		 * Set the coupon label in cart
		 *
		 * @param $string
		 * @param $coupon
		 *
		 * @return string
		 * @author   Emanuela Castorina
		 *
		 * @since    1.0.0
		 * @internal param $label
		 */
		public function coupon_label( $string, $coupon ) {

			$points_coupon_label = apply_filters( 'ywpar_coupon_label', __( 'Redeem points', 'yith-woocommerce-points-and-rewards' ) );

			return $this->check_coupon_is_ywpar( $coupon ) ? esc_html( $points_coupon_label ) : $string;

		}

		/**
		 * Set the message when the discount is applied with success
		 *
		 * @param $message
		 * @param $message_code
		 * @param $coupon
		 *
		 * @return string
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function coupon_rewards_message( $message, $message_code, $coupon ) {
			$message_changed = $this->check_coupons_in_cart( yit_get_prop( $coupon, 'code' ) );
			$is_par          = $this->check_coupon_is_ywpar( $coupon );

			$m = $is_par ? apply_filters( 'ywpar_discount_applied_message', __( 'Reward Discount Applied Successfully', 'yith-woocommerce-points-and-rewards' ) ) : $message;
			if ( $message_changed ) {
				switch ( $message_changed ) {
					case 'removed_par':
						if ( ! $is_par ) {
							$m = __( 'Reward Discount has been removed. You can\'t use this discount with other coupons.', 'yith-woocommerce-points-and-rewards' );
						} else {
							$m = __( 'You can\'t use this coupon with other coupons', 'yith-woocommerce-points-and-rewards' );
						}
						break;
					case 'removed_wc_coupon':
						if ( $is_par ) {
							$m = __( 'Coupon has been removed. You can\'t use this coupon with Rewards Discount', 'yith-woocommerce-points-and-rewards' );
						} else {
							$m = __( 'You can\'t use this coupon with Rewards Discount', 'yith-woocommerce-points-and-rewards' );
						}
						break;
					default:
				}
			}

			if ( $message_code === WC_Coupon::WC_COUPON_SUCCESS ) {
				return $m;
			} else {
				return $message;
			}
		}

		/**
		 * Return the discount amount
		 *
		 * @return float
		 * @author Emanuela Castorina
		 * @since  1.0.0
		 */
		public function get_discount_amount() {
			$discount = 0;
			if ( WC()->session !== null ) {
				$discount = WC()->session->get( 'ywpar_coupon_code_discount' );
			}

			return $discount;
		}

		/**
		 * Register the coupon amount and points in the post meta of order
		 * if there's a rewards
		 *
		 * @param $order_id
		 *
		 * @return mixed
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function add_order_meta( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( version_compare( WC()->version, '3.7.0', '<' ) ) {
				$used_coupons = $order->get_used_coupons();
			} else {
				$used_coupons = $order->get_coupon_codes();
			}

			// check if the coupon was used in the order
			if ( ! $coupon = $this->check_coupon_is_ywpar( $used_coupons ) ) {
				return;
			}

			yit_save_prop(
				$order,
				array(
					'_ywpar_coupon_amount' => WC()->session->get( 'ywpar_coupon_code_discount' ),
					'_ywpar_coupon_points' => WC()->session->get( 'ywpar_coupon_code_points' ),
				),
				false,
				true
			);
		}

		/**
		 * Deduct the point from the user total points
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param $order
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Emanuela Castorina
		 *
		 * @internal param $order_id
		 */
		public function deduce_order_points( $order ) {
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			$customer_user = $order->get_customer_id();

			if ( version_compare( WC()->version, '3.7.0', '<' ) ) {
				$used_coupons = $order->get_used_coupons();
			} else {
				$used_coupons = $order->get_coupon_codes();
			}

			// check if the coupon was used in the order
			if ( ! $coupon = $this->check_coupon_is_ywpar( $used_coupons ) ) {
				return;
			}

			$points          = yit_get_prop( $order, '_ywpar_coupon_points' );
			$discount_amount = yit_get_prop( $order, '_ywpar_coupon_amount' );
			$redemped_points = yit_get_prop( $order, '_ywpar_redemped_points' );

			if ( $redemped_points != '' ) {
				return;
			}

			if ( $customer_user ) {
				$current_discount_total_amount = (int) get_user_meta( $customer_user, '_ywpar_user_total_discount', true );
				update_user_meta( $customer_user, '_ywpar_user_total_discount', $current_discount_total_amount + $discount_amount );
				wp_cache_flush();
				if ( apply_filters( 'ywpar_update_wp_cache', false ) ) {
					$cached_user_meta                               = wp_cache_get( $customer_user, 'user_meta' );
					$cached_user_meta['_ywpar_user_total_discount'] = array( $current_discount_total_amount + $discount_amount );
					wp_cache_set( $customer_user, $cached_user_meta, 'user_meta' );
				}

				yit_save_prop( $order, '_ywpar_redemped_points', $points, false, true );
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer_user, - $points, 'redeemed_points', '', yit_get_prop( $order, 'id' ) );
				$order->add_order_note( sprintf( _x( '%1$d %2$s to get a reward', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), - $points, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );
			}

		}

		/**
		 * Return the conversion rate rewards based on the role of users
		 *
		 * @param string $currency
		 * @return float
		 * @author Emanuela Castorina
		 * @since  1.0.0
		 */
		public function get_conversion_rate_rewards( $currency = '' ) {

			$current_currency      = apply_filters( 'ywpar_multi_currency_current_currency', get_woocommerce_currency() );
			$currency              = empty( $currency ) ? $current_currency : $currency;
			$conversions           = YITH_WC_Points_Rewards()->get_option( 'rewards_conversion_rate' );
			$conversion            = isset( $conversions[ $currency ] ) ? $conversions[ $currency ] : array(
				'money'  => 0,
				'points' => 0,
			);
			$conversion            = apply_filters( 'ywpar_rewards_conversion_rate', $conversion );
			$conversion['money']   = ( empty( $conversion['money'] ) ) ? 1 : $conversion['money'];
			$conversion['points']  = ( empty( $conversion['points'] ) ) ? 1 : $conversion['points'];
			$conversion_rate_level = YITH_WC_Points_Rewards()->get_option( 'rewards_points_level' );

			if ( is_user_logged_in() ) {
				$current_user    = wp_get_current_user();
				$conversion_rate = abs( $conversion['points'] / $conversion['money'] );
				if ( YITH_WC_Points_Rewards()->get_option( 'rewards_points_for_role' ) == 'yes' ) {
					$conversion_by_role = YITH_WC_Points_Rewards()->get_option( 'rewards_points_role_rewards_fixed_conversion_rate' );

					if ( isset( $conversion_by_role['role_conversion'] ) ) {
						foreach ( $conversion_by_role['role_conversion'] as $conv_role ) {
							if ( isset( $conv_role['role'] ) ) {
								$current_role = $conv_role['role'];
								foreach ( $current_user->roles as $role ) {
									if ( $current_role == $role ) {

										$c = $conv_role;
										$c = isset( $c[ $currency ] ) ? $c[ $currency ] : array(
											'money'  => 0,
											'points' => 0,
										);
										if ( $c['points'] != '' && $c['money'] != '' && $c['money'] != 0 ) {
											$current_conversion_rate = abs( $c['points'] / $c['money'] );

											if ( ( $conversion_rate_level == 'high' && $current_conversion_rate <= $conversion_rate ) || ( $conversion_rate_level == 'low' && $current_conversion_rate > $conversion_rate ) ) {
												$conversion_rate = $current_conversion_rate;
												$conversion      = $c;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return apply_filters( 'ywpar_rewards_conversion_rate', $conversion );
		}

		/**
		 * Return the conversion percentual rate rewards
		 *
		 * @param string $currency
		 * @return array
		 * @author Emanuela Castorina
		 * @since  1.0.0
		 */
		public function get_conversion_percentual_rate_rewards( $currency = '' ) {

			$current_currency = apply_filters( 'ywpar_multi_currency_current_currency', get_woocommerce_currency() );
			$currency         = empty( $currency ) ? $current_currency : $currency;

			$conversions = YITH_WC_Points_Rewards()->get_option( 'rewards_percentual_conversion_rate' );
			$conversion  = isset( $conversions[ $currency ] ) ? $conversions[ $currency ] : array(
				'points'   => 0,
				'discount' => 0,
			);
			$conversion  = apply_filters( 'ywpar_rewards_percentual_conversion_rate', $conversion );

			$conversion['points']   = ( empty( $conversion['points'] ) ) ? 1 : $conversion['points'];
			$conversion['discount'] = ( empty( $conversion['discount'] ) ) ? 1 : $conversion['discount'];

			$conversion_rate_level = YITH_WC_Points_Rewards()->get_option( 'rewards_points_level' );

			if ( is_user_logged_in() ) {
				$current_user    = wp_get_current_user();
				$conversion_rate = abs( $conversion['points'] / $conversion['discount'] );
				if ( YITH_WC_Points_Rewards()->get_option( 'rewards_points_for_role' ) == 'yes' ) {
					$conversion_by_role = YITH_WC_Points_Rewards()->get_option( 'rewards_points_role_rewards_percentage_conversion_rate' );
					if ( isset( $conversion_by_role['role_conversion'] ) ) {
						foreach ( $conversion_by_role['role_conversion'] as $conv_role ) {
							if ( isset( $conv_role['role'] ) ) {
								$current_role = $conv_role['role'];
								foreach ( $current_user->roles as $role ) {
									$c = $conv_role;
									$c = isset( $c[ $currency ] ) ? $c[ $currency ] : array(
										'points'   => 0,
										'discount' => 0,
									);
									if ( $c['points'] != '' && $c['discount'] != '' && $c['discount'] != 0 ) {
										$current_conversion_rate = abs( $c['points'] / $c['discount'] );

										if ( ( $conversion_rate_level == 'high' && $current_conversion_rate <= $conversion_rate ) || ( $conversion_rate_level == 'low' && $current_conversion_rate > $conversion_rate ) ) {
											$conversion_rate = $current_conversion_rate;
											$conversion      = $c;
										}
									}
								}
							}
						}
					}
				}
			}

			return apply_filters( 'ywpar_rewards_percentual_conversion_rate', $conversion );
		}

		/**
		 * Get the rewarded points of a user from the user meta if exists or from the database if
		 * do not exist. In this last case the value is saved on the user meta
		 *
		 * @param $user_id
		 * @return int
		 * @since 1.3.0
		 */
		public function get_user_rewarded_points( $user_id ) {
			global $wpdb;

			// $rewarded_points = get_user_meta( $user_id, '_ywpar_rewarded_points', true );

			// if ( '' === $rewarded_points ) {
			$table_name      = $wpdb->prefix . 'yith_ywpar_points_log';
			$query           = "SELECT SUM(pl.amount) FROM $table_name as pl where pl.user_id = $user_id AND ( pl.action IN ( 'redeemed_points', 'order_refund', 'admin_action', 'order_cancelled', 'expired_points') AND pl.amount < 0 )";
			$rewarded_points = $wpdb->get_var( $query );

			$rewarded_points = is_null( $rewarded_points ) ? 0 : absint( $rewarded_points );
			update_user_meta( $user_id, '_ywpar_rewarded_points', $rewarded_points );

			// }

			return (int) $rewarded_points;

		}

		/**
		 * Set user rewarded points, add $rewarded_points to the user meta '_ywpar_rewarded_points'
		 *
		 * @param int $user_id
		 * @param int $rewarded_point
		 *
		 * @return void
		 * @since 1.3.0
		 */
		public function set_user_rewarded_points( $user_id, $rewarded_point ) {
			$new_rewarded_points = $rewarded_point + (int) $this->get_user_rewarded_points( $user_id );
			update_user_meta( $user_id, '_ywpar_rewarded_points', $new_rewarded_points );
			wp_cache_flush();
		}


		/**
		 * Calculate the points of a product/variation for a single item
		 *
		 * @param float $discount_amount
		 *
		 * @return  int $points
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function calculate_rewards_discount( $discount_amount = 0.0 ) {

			$user_id       = get_current_user_id();
			$points_usable = get_user_meta( $user_id, '_ywpar_user_total_points', true );

			if ( $points_usable <= 0 ) {
				return false;
			}

			$items = WC()->cart->get_cart();

			$this->max_discount = 0;
			$this->max_points   = 0;

			if ( $this->get_conversion_method() == 'fixed' ) {
				$conversion = $this->get_conversion_rate_rewards();

				// get the items of cart
				foreach ( $items as $item => $values ) {
					$product_id = ( isset( $values['variation_id'] ) && $values['variation_id'] != 0 ) ? $values['variation_id'] : $values['product_id'];

					$item_price       = apply_filters( 'ywpar_calculate_rewards_discount_item_price', ywpar_get_price( $values['data'] ), $values, $product_id );
					$product_discount = $this->calculate_product_max_discounts( $product_id, $item_price, $values );
					if ( $product_discount != 0 ) {
						$this->max_discount += $product_discount * $values['quantity'];
					}
				}

				$general_max_discount = YITH_WC_Points_Rewards()->get_option( 'max_points_discount' );

				if ( apply_filters( 'ywpar_exclude_taxes_from_calculation', false ) ) {
					$subtotal = ( (float) WC()->cart->get_subtotal() - (float) WC()->cart->get_discount_total() ) + $discount_amount;
				} else {
					$subtotal = ( ( (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax() ) - ( (float) WC()->cart->get_discount_total() + (float) WC()->cart->get_discount_tax() ) ) + $discount_amount;
				}

				if ( $subtotal <= $this->max_discount ) {
					$this->max_discount = $subtotal;
				}

				$this->max_discount = apply_filters( 'ywpar_set_max_discount_for_minor_subtotal', $this->max_discount, $subtotal );

				// check if there's a max discount amount
				if ( $general_max_discount != '' ) {
					$is_percent = strpos( $general_max_discount, '%' );
					if ( $is_percent === false ) {
						$max_discount = ( $subtotal >= $general_max_discount ) ? $general_max_discount : $subtotal;
					} else {
						$general_max_discount = (float) str_replace( '%', '', $general_max_discount );
						$max_discount         = $subtotal * $general_max_discount / 100;
					}

					if ( $max_discount < $this->max_discount ) {
						$this->max_discount = $max_discount;
					}
				}

				$this->max_discount = apply_filters( 'ywpar_calculate_rewards_discount_max_discount_fixed', $this->max_discount );
				$appfun             = apply_filters( 'ywpar_approx_function', 'ceil' );
				$this->max_points   = call_user_func( $appfun, $this->max_discount / $conversion['money'] * $conversion['points'] );

				if ( $this->max_points > $points_usable ) {
					$this->max_points   = $points_usable;
					$this->max_discount = $this->max_points / $conversion['points'] * $conversion['money'];
				}
			} elseif ( $this->get_conversion_method() == 'percentage' ) {
				$conversion = $this->get_conversion_percentual_rate_rewards();

				foreach ( $items as $item => $values ) {
					$product_id       = ( isset( $values['variation_id'] ) && $values['variation_id'] != 0 ) ? $values['variation_id'] : $values['product_id'];
					$item_price       = apply_filters( 'ywpar_calculate_rewards_discount_item_price', ywpar_get_price( $values['data'] ), $values );
					$product_discount = $this->calculate_product_max_discounts_percentage( $product_id, $item_price, $values );
					if ( $product_discount != 0 ) {
						$this->max_discount += $product_discount * $values['quantity'];
					}
				}

				$subtotal_cart = ywpar_get_subtotal_cart();
				if ( $subtotal_cart != 0 ) {
					$cart_discount_percentual = $this->max_discount / $subtotal_cart * 100;
					$max_points               = round( $cart_discount_percentual / $conversion['discount'] ) * $conversion['points'];
					$cart_discount_percentual = round( $max_points / $conversion['points'] ) * $conversion['discount'];

					if ( $points_usable >= $max_points ) {
						$this->max_points              = $max_points;
						$this->max_percentual_discount = $cart_discount_percentual;
						$this->max_discount            = ( $subtotal_cart * $this->max_percentual_discount ) / 100;
					} else {
						// must be floor because to calculate the right max points
						$this->max_percentual_discount = floor( $points_usable / $conversion['points'] ) * $conversion['discount'];
						$this->max_points              = round( $this->max_percentual_discount / $conversion['discount'] ) * $conversion['points'];
						$this->max_discount            = ( $subtotal_cart * $this->max_percentual_discount ) / 100;
					}
				}

				$this->max_discount = apply_filters( 'ywpar_calculate_rewards_discount_max_discount_percentual', $this->max_discount );
			}
			$this->max_discount = apply_filters( 'ywpar_calculate_rewards_discount_max_discount', $this->max_discount, $this, $conversion );
			$this->max_points   = apply_filters( 'ywpar_calculate_rewards_discount_max_points', $this->max_points, $this, $conversion );

			return $this->max_discount;
		}

		/**
		 * @param $product_id
		 * @param $points_earned
		 *
		 * @return mixed
		 */
		public function calculate_price_worth( $product_id, $points_earned ) {

			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				return 0;
			}

			if ( $product->is_type( 'variable' ) ) {
				$variations  = $product->get_available_variations();
				$price_worth = array();
				if ( $variations ) {
					foreach ( $variations as $variation ) {
						$price_worth[ $variation['variation_id'] ] = $this->calculate_price_worth( $variation['variation_id'], YITH_WC_Points_Rewards_Earning()->calculate_product_points( $variation['variation_id'] ) );
					}

					$price_worth = array_unique( $price_worth );

					if ( count( $price_worth ) == 0 ) {
						$return = wc_price( 0 );
					} elseif ( count( $price_worth ) == 1 ) {
						$return = reset( $price_worth );

					} else {
						$return = min( $price_worth ) . '-' . max( $price_worth );
					}

					return $return;
				}
			}

			$product_price = ywpar_get_price( $product );

			$price_from_point_earned = YITH_WC_Points_Rewards_Earning()->get_price_from_point_earned( $points_earned );

			if ( $price_from_point_earned != $product_price ) {
				$product_price = $price_from_point_earned;
			}

			$max_discount = $this->calculate_product_max_discounts( $product_id, $product_price );

			$discount          = 0;
			$conversion_method = $this->get_conversion_method();

			if ( $conversion_method == 'fixed' ) {
				$conversion  = $this->get_conversion_rate_rewards();
				$point_value = $conversion['money'] / $conversion['points'];
				$discount    = $points_earned * $point_value;
				$discount    = $discount > $max_discount ? $max_discount : $discount;
			}

			// DO_ACTION : before_return_calculate_price_worth : action triggered before return calculate price worth
			do_action( 'before_return_calculate_price_worth' );

			return apply_filters( 'ywpar_calculate_product_discount', wc_price( $discount ), $product_id );

		}

		/**
		 * Calculate the max discount of a product.
		 *
		 * Check if some option is set on product or category if not the
		 * general conversion will be used.
		 *
		 * @param int $product_id
		 *
		 * @param int $price
		 *
		 * @return float|mixed|string
		 */
		public function calculate_product_max_discounts( $product_id, $price = 0, $_product = null ) {

			$product = wc_get_product( $product_id );

			$max_discount = ywpar_get_price( $product );

			$max_discount_updated = false;
			$general_max_discount = YITH_WC_Points_Rewards()->get_option( 'max_points_product_discount' );
			$max_product_discount = get_post_meta( $product_id, '_ywpar_max_point_discount', true );
			$product_price        = $price ? $price : ywpar_get_price( $product );

			if ( $max_product_discount != '' ) {
				$is_percent = strpos( $max_product_discount, '%' );
				if ( $is_percent === false ) {
					$max_discount = ( $product_price >= $max_product_discount ) ? $max_product_discount : $product_price;
				} else {
					$max_product_discount = str_replace( '%', '', $max_product_discount );
					$max_discount         = $product_price * $max_product_discount / 100;
				}
				$max_discount_updated = true;
			}

			if ( ! $max_discount_updated ) {
				if ( $product->is_type( 'variation' ) ) {
					$categories = get_the_terms( yit_get_base_product_id( $product ), 'product_cat' );
				} else {
					$categories = get_the_terms( $product_id, 'product_cat' );
				}

				if ( ! empty( $categories ) ) {
					$max_discount = $product_price; // reset the global discount

					foreach ( $categories as $term ) {
						$max_category_discount = get_term_meta( $term->term_id, 'max_point_discount', true );

						if ( $max_category_discount != '' ) {

							$is_percent = strpos( $max_category_discount, '%' );

							if ( $is_percent === false ) {
								$max_discount = ( $product_price >= $max_category_discount ) ? $max_category_discount : $product_price;
							} else {
								$max_category_discount = str_replace( '%', '', $max_category_discount );
								$max_discount          = $product_price * $max_category_discount / 100;
							}

							$max_discount_updated = true;
						}
					}
				}
			}

			if ( ! $max_discount_updated && $general_max_discount != '' ) {
				$is_percent = strpos( $general_max_discount, '%' );
				if ( $is_percent === false ) {
					$max_discount = ( $product_price >= $general_max_discount ) ? $general_max_discount : ywpar_get_price( $product );
				} else {
					$general_max_discount = str_replace( '%', '', $general_max_discount );
					$max_discount         = $product_price * $general_max_discount / 100;
				}
			}

			return apply_filters( 'ywpar_calculate_product_max_discounts', $max_discount, $product_id, $_product );
		}

		/**
		 * Check if the discount applied follow the rule in the setting about more
		 * coupons in the cart
		 *
		 * @param $coupon_code
		 *
		 * @return bool|string
		 */
		public function check_coupons_in_cart( $coupon_code ) {

			$ywpar_added_coupon   = $this->check_coupon_is_ywpar( array( $coupon_code ) );
			$applied_coupons      = WC()->cart->get_applied_coupons();
			$ywpar_coupon_in_cart = $this->check_coupon_is_ywpar( $applied_coupons );

			$other_coupons = YITH_WC_Points_Rewards()->get_option( 'other_coupons' );

			$message = false;

			switch ( $other_coupons ) {
				case 'both':
					break;
				case 'ywpar':
					if ( $ywpar_added_coupon instanceof WC_Coupon ) {
						foreach ( $applied_coupons as $coupon_cart_code ) {
							if ( $coupon_code != $coupon_cart_code ) {

								WC()->cart->remove_coupon( $coupon_cart_code );
								$message = 'removed_wc_coupon';
							}
						}
					} elseif ( $ywpar_coupon_in_cart instanceof WC_Coupon ) {

						WC()->cart->remove_coupon( $coupon_code );
						$message = 'removed_wc_coupon';
					}
					break;
				case 'wc_coupon':
					if ( ( $ywpar_added_coupon instanceof WC_Coupon ) && count( $applied_coupons ) > 1 && apply_filters( 'ywpar_check_ywpar_coupon_before_remove', true, $coupon_code, $applied_coupons, $ywpar_coupon_in_cart ) ) {
						WC()->cart->remove_coupon( $coupon_code );

						$message = 'removed_par';
					} elseif ( $ywpar_coupon_in_cart instanceof WC_Coupon && apply_filters( 'ywpar_check_ywpar_coupon_before_remove', true, $coupon_code, $applied_coupons, $ywpar_coupon_in_cart ) ) {
						$c = yit_get_prop( $ywpar_coupon_in_cart, 'code' );
						if ( $c != $coupon_code ) {
							WC()->cart->remove_coupon( $c );

							$message = 'removed_par';
						}
					}

					break;
				default:
			}

			return $message;
		}

		/**
		 * @param     $product_id
		 *
		 * @param int        $price
		 *
		 * @return float|int|string
		 */
		public function calculate_product_max_discounts_percentage( $product_id, $price = 0 ) {

			$max_discount         = 0;
			$max_discount_updated = false;
			$conversion           = $this->get_conversion_percentual_rate_rewards();
			$general_max_discount = YITH_WC_Points_Rewards()->get_option( 'max_percentual_discount' );
			$general_max_discount = empty( $general_max_discount ) ? 100 : $general_max_discount;

			$product                        = wc_get_product( $product_id );
			$product_price                  = $price ? $price : ywpar_get_price( $product );
			$redemption_percentage_discount = yit_get_prop( $product, '_ywpar_redemption_percentage_discount', true );
			$redemption_percentage_discount = str_replace( '%', '', $redemption_percentage_discount );

			if ( $redemption_percentage_discount != '' ) {
				$max_discount         = ( $this->max_percentual_discount / $conversion['discount'] ) * $redemption_percentage_discount * $product_price / 100;
				$max_discount_updated = true;
			}

			if ( ! $max_discount_updated ) {
				if ( $product->is_type( 'variation' ) ) {
					$categories = get_the_terms( yit_get_base_product_id( $product ), 'product_cat' );
				} else {
					$categories = get_the_terms( $product_id, 'product_cat' );
				}

				if ( ! empty( $categories ) ) {
					$max_discount = ywpar_get_price( $product ); // reset the global discount

					foreach ( $categories as $term ) {
						$redemption_category_discount = get_term_meta( $term->term_id, 'redemption_percentage_discount', true );
						$redemption_category_discount = str_replace( '%', '', $redemption_category_discount );

						if ( $redemption_category_discount != '' ) {
							$max_discount         = $redemption_category_discount * $product_price / 100;
							$max_discount_updated = true;
						}
					}
				}
			}

			if ( ! $max_discount_updated && $general_max_discount != '' ) {
				$max_discount = $product_price * $general_max_discount / 100;
			}

			$max_discount = ( $product_price >= $max_discount ) ? $max_discount : $product_price;

			return $max_discount;
		}

		/**
		 * Return the maximum discount of a product
		 *
		 * @param $product_id
		 *
		 * @return mixed
		 * @author  Emanuela Castorina
		 * @since   1.1.3
		 */
		public function calculate_product_discounts( $product_id ) {
			$discount          = 0;
			$conversion_method = $this->get_conversion_method();

			if ( $conversion_method == 'fixed' ) {
				$max_discount = $this->calculate_product_max_discounts( $product_id );
				$conversion   = $this->get_conversion_rate_rewards();
				$discount     = $max_discount / $conversion['points'] * $conversion['money'];
			}

			return apply_filters( 'ywpar_calculate_product_discount', $discount, $product_id );
		}

		/**
		 * Return the min and maximum discount of a product variable
		 *
		 * @param $product
		 *
		 * @return mixed
		 * @internal param $product_id
		 * @since    1.1.3
		 * @author   Emanuela Castorina
		 */
		public function calculate_product_discounts_on_variable( $product ) {

			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! is_object( $product ) ) {
				return 0;
			}

			if ( ! $product->is_type( 'variable' ) ) {
				return 0;
			}

			$variations = $product->get_available_variations();
			$discounts  = array();
			if ( ! empty( $variations ) ) {
				foreach ( $variations as $variation ) {
					$discounts[] = $this->calculate_product_discounts( $variation['variation_id'] );
				}
			}

			$discounts = array_unique( $discounts );

			if ( count( $discounts ) == 0 ) {
				$return = '';
			} elseif ( count( $discounts ) == 1 ) {
				$return = wc_price( $discounts[0] );
			} else {
				$return = wc_price( min( $discounts ) ) . '-' . wc_price( max( $discounts ) );

			}

			return apply_filters( 'calculate_product_discounts_on_variable', $return, $product );

		}

		/**
		 * Return the conversion method that can be used in the cart fore rewards
		 *
		 * @return  string
		 * @author  Emanuela Castorina
		 * @since   1.1.3
		 */
		public function get_conversion_method() {
			return apply_filters( 'ywpar_conversion_method', YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' ) );
		}

		/**
		 * Return the max points that can be used in the cart fore rewards
		 * must be called after the function calculate_points_and_discount
		 *
		 * @return  int
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function get_max_points() {
			return apply_filters( 'ywpar_rewards_max_points', $this->max_points );
		}

		/**
		 * Return the max discount that can be used in the cart fore rewards
		 * must be called after the function calculate_points_and_discount
		 *
		 * @return  float
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function get_max_discount() {
			return apply_filters( 'ywpar_rewards_max_discount', $this->max_discount );
		}

		/**
		 * Return the max discount that can be used in the cart fore rewards
		 * must be called after the function calculate_points_and_discount
		 *
		 * @return  float
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function get_max_percentual_discount() {
			return apply_filters( 'ywpar_rewards_max_percentual_discount', $this->max_percentual_discount );
		}

		/**
		 * Check if a YWPAR Coupons is in the list
		 *
		 * @param $coupon_list array|WC_Coupon
		 *
		 * @return bool|WC_Coupon
		 */
		public function check_coupon_is_ywpar( $coupon_list ) {

			if ( version_compare( WC()->version, '2.7', '<' ) ) {
				if ( is_array( $coupon_list ) && ! empty( $coupon_list ) ) {
					foreach ( $coupon_list as $coupon_in_cart_code ) {
						$coupon_in_cart = new WC_Coupon( $coupon_in_cart_code );

						return $this->label_coupon_prefix == $coupon_in_cart->code;
					}
				} elseif ( $coupon_list instanceof WC_Coupon ) {
					return $this->label_coupon_prefix == $coupon_list->code;
				}
			} else {
				if ( is_array( $coupon_list ) && ! empty( $coupon_list ) ) {
					foreach ( $coupon_list as $coupon_in_cart_code ) {
						$coupon_in_cart = new WC_Coupon( $coupon_in_cart_code );
						if ( $coupon_in_cart ) {
							// $meta = $coupon_in_cart->get_meta( 'ywpar_coupon' );
							$meta = yit_get_prop( $coupon_in_cart, 'ywpar_coupon' );

							if ( ! empty( $meta ) ) {
								return $coupon = $coupon_in_cart;
							}
						}
					}
				} elseif ( $coupon_list instanceof WC_Coupon ) {
					$var1 = $coupon_list->get_meta( 'ywpar_coupon' );

					return ! empty( $var1 );
				}
			}

			return false;
		}

		/**
		 * Return the coupon to apply
		 *
		 * @return WC_Coupon
		 */
		public function get_current_coupon() {

			if ( empty( $this->current_coupon_code ) ) {
				// check if in the cart
				$coupons_in_cart = WC()->cart->get_applied_coupons();

				foreach ( $coupons_in_cart as $coupon_in_cart_code ) {
					if ( $this->check_coupon_is_ywpar( $coupon_in_cart_code ) ) {
						$this->current_coupon_code = $coupon_in_cart_code;
						break;
					}
				}
			}

			if ( empty( $this->current_coupon_code ) ) {
				if ( is_user_logged_in() ) {
					$this->current_coupon_code = apply_filters( 'ywpar_coupon_code', $this->label_coupon_prefix . '_' . get_current_user_id(), $this->label_coupon_prefix );
				}
			}

			$coupon = empty( $this->current_coupon_code ) ? false : new WC_Coupon( $this->current_coupon_code );

			return $coupon;
		}

		/**
		 * Set cron to clear coupon
		 */
		public function ywpar_set_cron() {
			if ( ! wp_next_scheduled( 'ywpar_clean_cron' ) ) {
				$duration = apply_filters( 'ywpar_set_cron_time', 'daily' );
				wp_schedule_event( time(), $duration, 'ywpar_clean_cron' );
			}
		}

		/**
		 * Clear coupons after use
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function clear_coupons() {

			$delete_after_use = YITH_WC_Points_Rewards()->get_option( 'coupon_delete_after_use' );

			if ( $delete_after_use != 'yes' ) {
				return;
			}

			$args = array(
				'post_type'       => 'shop_coupon',
				'posts_per_pages' => - 1,
				'meta_key'        => 'ywpar_coupon',
				'meta_value'      => 1,
				'date_query'      => array(
					array(
						'column' => 'post_date_gmt',
						'before' => '1 day ago',
					),
				),
			);

			$coupons = get_posts( $args );

			if ( ! empty( $coupons ) ) {
				foreach ( $coupons as $coupon ) {
					wp_delete_post( $coupon->ID, true );
				}
			}
		}

		/**
		 * Returns the usage limit parameter to do a coupon. The function check the option 'other_coupons'.
		 * if this option is equal to 'ywpar' usage limit will be equal 1
		 *
		 * @return bool
		 */
		protected function get_usage_limit() {
			$opt = YITH_WC_Points_Rewards()->get_option( 'other_coupons' );

			return $usage_limit = $opt === 'ywpar' ? 1 : 0;
		}

		/**
		 * @param int|WC_Order $order
		 */

		public function rewards_order_points( $order ) {

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}
			$points_redemption = $order->get_meta( '_ywpar_redemped_points', true );
			if ( ! empty( $points_redemption ) ) {
				$customer_id = $order->get_customer_id();

				if ( $customer_id ) {
					YITH_WC_Points_Rewards()->add_point_to_customer( $customer_id, - $points_redemption, 'redeemed_points', '', $order->get_id() );
					$order->add_order_note( sprintf( _x( '%1$d %2$s to get a reward', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), - $points_redemption, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );

				}
			}
		}
	}
}


/**
 * Unique access to instance of YITH_WC_Points_Rewards_Redemption class
 *
 * @return \YITH_WC_Points_Rewards_Redemption
 */
function YITH_WC_Points_Rewards_Redemption() {
	return apply_filters( 'ywpar_wc_points_rewards_redemption_instance', YITH_WC_Points_Rewards_Redemption::get_instance() );
}
