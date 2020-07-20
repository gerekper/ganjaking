<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * YWPAR_Subscription class to add compatibility with YITH WooCommerce Affiliates Premium
 *
 * @class   YWPAR_Subscription
 * @package YITH WooCommerce Points and Rewards
 * @since   1.6.0
 * @author  YITH
 */
if ( ! class_exists( 'YWPAR_Subscription' ) ) {
	/**
	 * Class YWPAR_Subscription
	 */
	class YWPAR_Subscription {
		/**
		 * Single instance of the class
		 *
		 * @var \YWPAR_Subscription
		 * @since 1.6.0
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWPAR_Subscription
		 * @since 1.6.0
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
		 * Initialize plugin and registers actions and filters to be used.
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			// admin option
			add_filter( 'ywpar_points_settings', array( $this, 'admin_options' ) );
			add_filter( 'ywpar_add_order_points', array( $this, 'check_renew' ), 10, 2 );

			if ( 'yes' == YITH_WC_Points_Rewards()->get_option( 'earn_points_on_fee' ) ) {
				add_filter( 'ywpar_get_point_earned_price', array( $this, 'earn_points_on_fee' ), 10, 3 );
			}
			if ( 'yes' == YITH_WC_Points_Rewards()->get_option( 'earn_points_on_renew' ) ) {
				add_action( 'ywsbs_subscription_payment_complete', array( $this, 'assign_points_on_renew' ), 10, 2 );
			}
		}

		/**
		 *
		 * @param $result
		 * @param $order_id
		 *
		 * @return bool
		 */
		public function check_renew( $result, $order_id ) {
			$order      = wc_get_order( $order_id );
			$is_a_renew = yit_get_prop( $order, 'is_a_renew', true );

			return 'yes' == $is_a_renew;
		}

		/**
		 * Assign point to customer on renew order
		 *
		 * @param $subscription
		 * @param $order
		 */
		public function assign_points_on_renew( $subscription, $order ) {
			$parent_order = wc_get_order( $subscription->order_id );
			$customer     = $subscription->user_id;

			if ( ! $parent_order || ! $customer ) {
				return;
			}

			$point_earned = yit_get_prop( $parent_order, '_ywpar_points_earned', true );
			$is_set       = yit_get_prop( $order, '_ywpar_points_earned', true );
			if ( $is_set || ! $point_earned ) {
				return;
			}

			if ( $subscription->fee > 0 ) {
				$conversion   = yit_get_prop( $parent_order, '_ywpar_conversion_points' );
				$point_earned -= (float)$subscription->fee / $conversion['money'] * $conversion['points'];
			}

			if ( $point_earned > 0 ) {
				yit_save_prop( $order, '_ywpar_points_earned', $point_earned );
				$order->add_order_note( sprintf( _x( 'Customer earned %1$d %2$s for this purchase.', 'First placeholder: number of points; second placeholder: label of points', 'yith-woocommerce-points-and-rewards' ), $point_earned, YITH_WC_Points_Rewards()->get_option( 'points_label_plural' ) ), 0 );
				YITH_WC_Points_Rewards()->add_point_to_customer( $customer, $point_earned, 'renew_order', '', $order->get_id() );
			}

		}

		/**
		 * Add point to subscription product calculation if there's a fee.
		 *
		 * @param $price
		 * @param $currency
		 * @param $product
		 *
		 * @return mixed
		 */
		public function earn_points_on_fee( $price, $currency, $product ) {

			$is_subscription = function_exists( 'ywsbs_is_subscription_product' ) ? ywsbs_is_subscription_product( $product ) : YITH_WC_Subscription()->is_subscription( $product );

			if ( $is_subscription ) {
				$signup_fee = yit_get_prop( $product, '_ywsbs_fee' );
				if ( $signup_fee ) {
					$price += $signup_fee;
				}
			}

			return $price;
		}


		/**
		 *
		 * @param $options
		 *
		 * @return mixed
		 */
		public function admin_options( $options ) {

			$subscription_option = array(
				'subscription_title' => array(
					'name' => __( 'Subscription Settings', 'yith-woocommerce-points-and-rewards' ),
					'type' => 'title',
					'id'   => 'ywpar_subscription_title',
				),

				'earn_points_on_fee' => array(
					'name'      => __( 'Earn points on subscription fee', 'yith-woocommerce-points-and-rewards' ),
					'desc'      => '',
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => 'no',
					'id'        => 'ywpar_earn_points_on_fee',
				),

				'earn_points_on_renew' => array(
					'name'      => __( 'Earn points on renewal orders', 'yith-woocommerce-points-and-rewards' ),
					'desc'      => '',
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'default'   => 'no',
					'id'        => 'ywpar_earn_points_on_renew',
				),

				'label_renew_order' => array(
					'name'      => __( 'Renew order label', 'yith-woocommerce-points-and-rewards' ),
					'desc'      => '',
					'type'      => 'yith-field',
					'yith-type' => 'text',
					'default'   => __( 'Renew order', 'yith-woocommerce-points-and-rewards' ),
					'id'        => 'ywpar_label_renew_order',
					'deps'      => array(
						'id'    => 'ywpar_earn_points_on_renew',
						'value' => 'yes',
						'type'  => 'hide',
					),
				),

				'subscription_title_end' => array(
					'type' => 'sectionend',
					'id'   => 'ywpar_subscription_title_end',
				),
			);

			$options['points'] = array_merge( $options['points'], $subscription_option );

			return $options;
		}

	}


	/**
	 * Unique access to instance of YWPAR_Subscription class
	 *
	 * @return \YWPAR_Subscription
	 */
	function YWPAR_Subscription() {
		return YWPAR_Subscription::get_instance();
	}

	YWPAR_Subscription();
}
