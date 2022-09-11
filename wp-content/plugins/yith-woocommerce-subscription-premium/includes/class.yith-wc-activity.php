<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_WC_Activity is an log of all transactions
 *
 * @class   YITH_WC_Activity
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Activity' ) ) {
	/**
	 * Class YITH_WC_Activity
	 */
	class YITH_WC_Activity {


		/**
		 * Single instance of the class.
		 *
		 * @var YITH_WC_Activity
		 */
		protected static $instance;

		/**
		 * Activities.
		 *
		 * @var array
		 */
		protected $activities;

		/**
		 * Activity Table name.
		 *
		 * @var string
		 */
		public $table_name = '';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Activity
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			global $wpdb;

			$this->table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
			$this->fill_activities();

			// remove activities if a subscription is deleted.
			add_action( 'deleted_post', array( $this, 'delete_activities' ) );
		}

		/**
		 * Add new activity
		 *
		 * Initialize class and registers actions and filters to be used.
		 *
		 * @param int    $subscription_id Subscription id.
		 * @param string $activity Activity.
		 * @param string $status Status.
		 * @param int    $order Order ID.
		 * @param string $description Description.
		 *
		 * @since 1.0.0
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

			$wpdb->insert( $this->table_name, $data ); // phpcs:ignore
		}

		/**
		 * Fill the activity array.
		 */
		public function fill_activities() {

			$this->activities = array(
				'new'            => esc_html_x( 'New Subscription', 'new subscription has been created', 'yith-woocommerce-subscription' ),
				'renew-order'    => esc_html_x( 'Renewal Order', 'new order has been created for the subscription', 'yith-woocommerce-subscription' ),
				'activated'      => esc_html_x( 'Subscription Activated', '', 'yith-woocommerce-subscription' ),
				'trial'          => esc_html_x( 'Started Trial Period', '', 'yith-woocommerce-subscription' ),
				'cancelled'      => esc_html_x( 'Cancelled Subscription', 'subscription cancelled by shop manager or customer', 'yith-woocommerce-subscription' ),
				'auto-cancelled' => esc_html_x( 'Auto Cancelled Subscription', 'subscription cancelled by system', 'yith-woocommerce-subscription' ),
				'expired'        => esc_html_x( 'Subscription Expired', 'subscription expired', 'yith-woocommerce-subscription' ),
				'switched'       => esc_html_x( 'Subscription Switched to another subscription', 'subscription switched', 'yith-woocommerce-subscription' ),
				'resumed'        => esc_html_x( 'Subscription Resumed', 'subscription resumed by shop manager or customer', 'yith-woocommerce-subscription' ),
				'auto-resumed'   => esc_html_x( 'Subscription Automatic Resumed', 'subscription resumed for expired pause', 'yith-woocommerce-subscription' ),
				'paused'         => esc_html_x( 'Subscription Paused', 'subscription paused by shop manager or customer', 'yith-woocommerce-subscription' ),
				'suspended'      => esc_html_x( 'Subscription Suspended', 'subscription suspended automatically due to non-payment', 'yith-woocommerce-subscription' ),
				'overdue'        => esc_html_x( 'Subscription Overdue', 'subscription overdue automatically due to non-payment', 'yith-woocommerce-subscription' ),
				'failed-payment' => esc_html_x( 'Failed Payment', 'subscription failed payment', 'yith-woocommerce-subscription' ),
				'trashed'        => esc_html_x( 'Subscription Trashed', 'subscription was trashed', 'yith-woocommerce-subscription' ),
				'changed'        => esc_html_x( 'Subscription Changed', 'subscription was changed', 'yith-woocommerce-subscription' ),

			);

		}

		/**
		 * Get activity by subscription.
		 *
		 * @param int $subscription_id Subscription ID.
		 * @param int $limit Limit of activities.
		 * @return array|null|object
		 */
		public function get_activity_by_subscription( $subscription_id, $limit = false ) {
			global $wpdb;

			if ( ! $limit ) {
				$results = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription = %d ORDER by timestamp_date DESC ", $subscription_id ) );  // phpcs:ignore
			} else {
				$results = $wpdb->get_results( $wpdb->prepare( "Select * from {$this->table_name} WHERE subscription = %d ORDER by timestamp_date DESC LIMIT %d", $subscription_id, $limit ) );  // phpcs:ignore
			}

			return $results;
		}


		/**
		 * Remove the activities of subscription.
		 *
		 * @param int $subscription_id Subscription id.
		 *
		 * @return array|null|object
		 */
		public function remove_activities_of_subscription( $subscription_id ) {
			global $wpdb;

			$results = $wpdb->get_results( $wpdb->prepare( "DELETE from {$this->table_name} WHERE subscription = %d", $subscription_id ) );  // phpcs:ignore

			return $results;

		}


		/**
		 * Delete all activities of a subscription
		 *
		 * @param int $post_id Post ID.
		 */
		public function delete_activities( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && YITH_YWSBS_POST_TYPE === $post->post_type ) {
				$this->remove_activities_of_subscription( $post_id );
			}
		}

		/**
		 * Check if there are activities on table
		 *
		 * @return bool
		 * @since 2.1.0
		 */
		public function is_activities_list_empty() {
			global $wpdb;
			$count = $wpdb->get_var( "Select count(0) as c from {$this->table_name}" );  // phpcs:ignore
			return 0 === (int) $count;
		}
	}

}

/**
 * Unique access to instance of YITH_WC_Activity class
 *
 * @return YITH_WC_Activity
 */
function YITH_WC_Activity() { //phpcs:ignore
	return YITH_WC_Activity::get_instance();
}
