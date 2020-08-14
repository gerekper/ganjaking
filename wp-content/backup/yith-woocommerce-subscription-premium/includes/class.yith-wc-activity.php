<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * YITH_WC_Activity is an log of all transactions
 *
 * @class   YITH_WC_Activity
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Activity' ) ) {

	class YITH_WC_Activity {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Activity
		 */
		protected static $instance;

		/**
		 * @var
		 */
		protected $activities;

		/**
		 * @var string
		 */
		public $table_name = '';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Activity
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
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			global $wpdb;

			$this->table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
			$this->fill_activities();

			// remove activities if a subscription is deleted
			add_action( 'deleted_post', array( $this, 'delete_activities' ) );
		}

		/**
		 * Add new activity
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @param $subscription_id
		 * @param string          $activity
		 * @param string          $status
		 * @param int             $order
		 * @param string          $description
		 */
		public function add_activity( $subscription_id, $activity = '', $status = 'success', $order = 0, $description = '' ) {
			global $wpdb;

			$activity  = $this->activities[ $activity ];
			$order     = $order ? $order : 0;
			$post_date = current_time( 'mysql' );
			$data      = array(
				'activity'       => $activity,
				'status'         => $status,
				'subscription'   => $subscription_id,
				'order'          => $order,
				'description'    => esc_sql( $description ),
				'timestamp_date' => $post_date,
			);
			$result    = $wpdb->insert( $this->table_name, $data );

		}

		/**
		 *
		 */
		public function fill_activities() {

			$this->activities = array(
				'new'            => _x( 'New Subscription', 'new subscription has been created', 'yith-woocommerce-subscription' ),
				'renew-order'    => _x( 'Renewal Order', 'new order has been created for the subscription', 'yith-woocommerce-subscription' ),
				'activated'      => _x( 'Subscription Activated', '', 'yith-woocommerce-subscription' ),
				'trial'          => _x( 'Started Trial Period', '', 'yith-woocommerce-subscription' ),
				'cancelled'      => _x( 'Cancelled Subscription', 'subscription cancelled by shop manager or customer', 'yith-woocommerce-subscription' ),
				'auto-cancelled' => _x( 'Auto Cancelled Subscription', 'subscription cancelled by system', 'yith-woocommerce-subscription' ),
				'expired'        => _x( 'Subscription Expired', 'subscription expired', 'yith-woocommerce-subscription' ),
				'switched'       => _x( 'Subscription Switched to another subscription', 'subscription switched', 'yith-woocommerce-subscription' ),
				'resumed'        => _x( 'Subscription Resumed', 'subscription resumed by shop manager or customer', 'yith-woocommerce-subscription' ),
				'auto-resumed'   => _x( 'Subscription Automatic Resumed', 'subscription resumed for expired pause', 'yith-woocommerce-subscription' ),
				'paused'         => _x( 'Subscription Paused', 'subscription paused by shop manager or customer', 'yith-woocommerce-subscription' ),
				'suspended'      => _x( 'Subscription Suspended', 'subscription suspended automatically due to non-payment', 'yith-woocommerce-subscription' ),
				'failed-payment' => _x( 'Failed Payment', 'subscription failed payment', 'yith-woocommerce-subscription' ),
				'trashed'        => _x( 'Subscription Trashed', 'subscription was trashed', 'yith-woocommerce-subscription' ),
				'changed'        => _x( 'Subscription Changed', 'subscription was changed', 'yith-woocommerce-subscription' ),

			);

		}

		/**
		 * @param $subscription_id
		 *
		 * @return array|null|object
		 */
		public function get_activity_by_subscription( $subscription_id ) {
			global $wpdb;
			$query   = $wpdb->prepare( "Select * from $this->table_name WHERE subscription = %d ORDER by timestamp_date DESC", $subscription_id );
			$results = $wpdb->get_results( $query );

			return $results;
		}


		/**
		 * @param $subscription_id
		 *
		 * @return array|null|object
		 */
		public function remove_activities_of_subscription( $subscription_id ) {
			global $wpdb;

			$query   = $wpdb->prepare( "DELETE from $this->table_name WHERE subscription = %d", $subscription_id );
			$results = $wpdb->get_results( $query );

			return $results;

		}


		/**
		 * Delete all activities of a subscription
		 *
		 * @param $post_id
		 *
		 * @internal param $post
		 */
		public function delete_activities( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && $post->post_type == YITH_WC_Subscription()->post_name ) {
				$this->remove_activities_of_subscription( $post->ID );
			}
		}

	}

}

/**
 * Unique access to instance of YITH_WC_Activity class
 *
 * @return \YITH_WC_Activity
 */
function YITH_WC_Activity() {
	return YITH_WC_Activity::get_instance();
}
