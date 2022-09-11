<?php
/**
 * Implements helper functions for YITH WooCommerce Subscription
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'admin_init', 'ywsbs_update_2_0' );
if ( ! function_exists( 'ywsbs_update_2_0' ) ) {
	/**
	 * Update script.
	 */
	function ywsbs_update_2_0() {
		$ywsbs_option_version = get_option( 'ywsbs_update_2_0', '2.0.0' );
		$check_old_option     = get_option( 'ywsbs_enabled' );

		if ( $check_old_option || ( $ywsbs_option_version && version_compare( $ywsbs_option_version, '2.0.0', '<' ) ) ) {

			// porting changing subscription status.
			$old_enable_overdue             = get_option( 'ywsbs_enable_overdue_period', 'no' );
			$old_ywsbs_overdue_start_period = get_option( 'ywsbs_overdue_start_period', 0 );
			$old_ywsbs_overdue_period       = get_option( 'ywsbs_overdue_period', 0 );

			$old_enable_suspend                = get_option( 'ywsbs_enable_suspension_period' );
			$old_ywsbs_suspension_start_period = get_option( 'ywsbs_suspension_start_period', 'no' );
			$old_ywsbs_suspension_period       = get_option( 'ywsbs_suspension_period', 0 );

			$old_ywsbs_cancel_start_period = get_option( 'ywsbs_cancel_start_period', 48 );

			$ywsbs_change_status_after_renew_order_creation = array(
				'status'   => 'suspended',
				'wait_for' => 48,
				'length'   => 20,
			);

			$ywsbs_change_status_after_renew_order_creation_step_2 = array(
				'status'   => 'cancelled',
				'wait_for' => 48,
				'length'   => 0,
			);

			if ( 'yes' === $old_enable_overdue ) {
				$ywsbs_change_status_after_renew_order_creation['status']   = 'overdue';
				$ywsbs_change_status_after_renew_order_creation['wait_for'] = $old_ywsbs_overdue_start_period;
				$ywsbs_change_status_after_renew_order_creation['length']   = $old_ywsbs_overdue_period;
			} elseif ( 'yes' === $old_enable_suspend ) {
				$ywsbs_change_status_after_renew_order_creation['status']   = 'suspended';
				$ywsbs_change_status_after_renew_order_creation['wait_for'] = $old_ywsbs_suspension_start_period;
				$ywsbs_change_status_after_renew_order_creation['length']   = $old_ywsbs_suspension_period;
			} else {
				if ( $old_enable_suspend ) {
					$ywsbs_change_status_after_renew_order_creation['status']   = 'cancelled';
					$ywsbs_change_status_after_renew_order_creation['wait_for'] = $old_ywsbs_cancel_start_period;
				}
			}

			if ( 'overdue' === $ywsbs_change_status_after_renew_order_creation['status'] ) {
				if ( 'yes' === $old_enable_suspend ) {
					$ywsbs_change_status_after_renew_order_creation_step_2['status']   = 'suspended';
					$ywsbs_change_status_after_renew_order_creation_step_2['length']   = $old_ywsbs_suspension_period;
					$ywsbs_change_status_after_renew_order_creation_step_2['wait_for'] = 0;
				}
			}

			update_option( 'ywsbs_change_status_after_renew_order_creation', $ywsbs_change_status_after_renew_order_creation );
			update_option( 'ywsbs_change_status_after_renew_order_creation_step_2', $ywsbs_change_status_after_renew_order_creation_step_2 );

			// delete old and unused options.
			delete_option( 'ywsbs_enabled' );
			delete_option( 'ywsbs_enable_overdue_period' );
			delete_option( 'ywsbs_overdue_start_period' );
			delete_option( 'ywsbs_overdue_period' );
			delete_option( 'ywsbs_enable_suspension_period' );
			delete_option( 'ywsbs_suspension_start_period' );
			delete_option( 'ywsbs_suspension_period' );
			delete_option( 'ywsbs_cancel_start_period' );
		}

		update_option( 'ywsbs_update_2_0', '2.0.0' );
	}
}

add_action( 'admin_init', 'ywsbs_schedule_report_import', 30 );

if ( ! function_exists( 'ywsbs_schedule_report_import' ) ) {
	/**
	 * Schedule the import report
	 */
	function ywsbs_schedule_report_import() {
		$ywsbs_option_version = get_option( 'ywsbs_schedule_report_import', '2.2.0' );

		if ( version_compare( $ywsbs_option_version, '2.3.0', '<' ) ) {

			$schedule_info = array(
				'hook' => 'ywsbs_import_subscriptions',
				'args' => array(
					'limit' => 10,
					'page'  => 1,
				),
			);

			$has_hook_scheduled = as_next_scheduled_action( $schedule_info['hook'], $schedule_info['args'] );

			if ( ! $has_hook_scheduled ) {
				as_schedule_single_action( time() + 5, $schedule_info['hook'], $schedule_info['args'] );
			}
		}

		update_option( 'ywsbs_schedule_report_import', '2.3.0', 1 );
	}
}
