<?php
/**
 * CRON
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Cron' ) ) {
	/**
	 * Notifier class.
	 *
	 * @since    1.0.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Cron {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMBS_Notifier
		 * @since 1.0.0
		 */
		private static $_instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMBS_Messages_Manager_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		private function __construct() {
			add_action( 'yith_wcmbs_check_expiring_membership', array( $this, 'check_expiring_membership' ) );
			add_action( 'yith_wcmbs_check_expired_membership', array( $this, 'check_expired_membership' ) );
			add_action( 'yith_wcmbs_check_credits_in_membership', array( $this, 'check_credits_in_membership' ) );
			add_action( 'yith_wcmbs_delete_transients_cron', array( $this, 'delete_transients' ) );

			add_action( 'wp_loaded', array( $this, 'set_cron' ), 30 );
		}

		public function set_cron() {
			if ( ! wp_next_scheduled( 'yith_wcmbs_check_expiring_membership' ) ) {
				wp_schedule_event( time(), 'daily', 'yith_wcmbs_check_expiring_membership' );
			}

			if ( ! wp_next_scheduled( 'yith_wcmbs_check_expired_membership' ) ) {
				wp_schedule_event( time(), 'daily', 'yith_wcmbs_check_expired_membership' );
			}

			if ( ! wp_next_scheduled( 'yith_wcmbs_check_credits_in_membership' ) ) {
				wp_schedule_event( time(), 'daily', 'yith_wcmbs_check_credits_in_membership' );
			}

			// Clear old hook for deleting transients daily.
			wp_clear_scheduled_hook( 'yith_wcmbs_delete_transients' );

			if ( ! wp_next_scheduled( 'yith_wcmbs_delete_transients_cron' ) ) {
				wp_schedule_event( strtotime( 'now midnight' ), 'daily', 'yith_wcmbs_delete_transients_cron' );
			}
		}


		public function check_expiring_membership() {
			$expiring_days = absint( apply_filters( 'yith_wcmbs_membership_max_days_number_to_send_expiring_email', 10 ) );

			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_status',
					'value'   => array( 'active', 'resumed' ),
					'compare' => 'IN',
				),
				array(
					'key'     => '_end_date',
					'value'   => 'unlimited',
					'compare' => '!=',
				),
				array(
					'key'     => '_end_date',
					'value'   => strtotime( 'tomorrow midnight' ) + $expiring_days * DAY_IN_SECONDS,
					'compare' => '<=',
				),
			);

			$all_membership = YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query );

			if ( ! empty( $all_membership ) ) {
				foreach ( $all_membership as $membership ) {
					if ( $membership instanceof YITH_WCMBS_Membership ) {
						$membership->check_is_expiring();
					}
				}
			}
		}

		public function check_expired_membership() {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_status',
					'value'   => array( 'active', 'resumed', 'expiring' ),
					'compare' => 'IN',
				),
				array(
					'key'     => '_end_date',
					'value'   => 'unlimited',
					'compare' => '!=',
				),
				array(
					'key'     => '_end_date',
					'value'   => strtotime( 'tomorrow midnight' ),
					'compare' => '<=',
				),
			);

			$all_membership = YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query );

			if ( ! empty( $all_membership ) ) {
				foreach ( $all_membership as $membership ) {
					if ( $membership instanceof YITH_WCMBS_Membership ) {
						$membership->check_is_expired();
					}
				}
			}
		}

		public function check_credits_in_membership() {
			$plans                 = YITH_WCMBS_Manager()->get_plans();
			$plan_ids_with_credits = array();

			$today = strtotime( 'now midnight' );

			foreach ( $plans as $plan ) {
				if ( $plan->has_credits() ) {
					$plan_ids_with_credits[] = $plan->get_id();
				}
			}

			if ( ! empty( $plan_ids_with_credits ) ) {
				$meta_query = array(
					'relation' => 'AND',
					array(
						'key'     => '_status',
						'value'   => array( 'active', 'resumed', 'expiring' ),
						'compare' => 'IN',
					),
					array(
						'key'     => '_plan_id',
						'value'   => $plan_ids_with_credits,
						'compare' => 'IN',
					),
					array(
						'key'     => '_next_credits_update',
						'value'   => $today,
						'compare' => '<=',
					),
				);

				$all_membership = YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query );

				if ( ! empty( $all_membership ) ) {
					foreach ( $all_membership as $membership ) {
						if ( $membership instanceof YITH_WCMBS_Membership ) {
							$membership->check_credits();
						}
					}
				}
			}
		}

		/**
		 * Delete transients daily to allow coherence for delay.
		 */
		public function delete_transients() {
			if ( apply_filters( 'yith_wcmbs_delete_transients_cron_enabled', true ) ) {
				do_action( 'yith_wcmbs_delete_transients' );
			}
		}

	}
}

/**
 * Unique access to instance of YITH_WCMBS_Cron class
 *
 * @return \YITH_WCMBS_Cron
 * @since 1.0.0
 */
function YITH_WCMBS_Cron() {
	return YITH_WCMBS_Cron::get_instance();
}