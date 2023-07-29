<?php
/**
 * WooCommerce Customer/Order/Coupon Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Automations;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order Export Automations Scheduler
 *
 * Handles scheduling export automations
 *
 * @since 5.0.0
 */
class Scheduler {


	/** @var string the callback hook used to execute scheduled actions */
	protected static $automation_hook = 'wc_customer_order_export_do_scheduled_automation';

	/** @var string the callback hook used to cleanup exports */
	protected static $cleanup_hook = 'wc_customer_order_export_cleanup_exports';

	/** @var string the callback hook used to validate automation schedules */
	protected static $validate_hook = 'wc_customer_order_export_validate_automations';


	/**
	 * Constructs the scheduler class.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		// listen for scheduled automations
		add_action( self::$automation_hook, static function( $automation_id ) { self::do_scheduled_automation( $automation_id ); } );

		// listen for automation object events for scheduling
		add_action( 'wc_customer_order_export_new_automation',             self::class . '::schedule_automation', 10, 2 );
		add_action( 'wc_customer_order_export_update_automation_schedule', self::class . '::update_automation_schedule', 10, 2 );
		add_action( 'wc_customer_order_export_delete_automation',          self::class . '::unschedule_automation' );

		/**
		 * Trigger order export when an order is processed or status updates.
		 *
		 * The priority needs to be higher than 60 to make sure this runs after the metabox save.
		 * @see Exported_By::save_order
		 */
		add_action( 'woocommerce_checkout_order_processed', self::class . '::auto_export_order', 100 );
		add_action( 'woocommerce_order_status_changed',     self::class . '::auto_export_order', 100 );

		// clean up expired exports
		add_action( 'init', self::class . '::schedule_cleanup' );
		add_action( self::$cleanup_hook, self::class . '::cleanup_expired_exports' );

		// validate scheduled automations
		add_action( 'init', self::class . '::schedule_validation' );
		add_action( self::$validate_hook, self::class . '::validate_scheduled_automations' );
	}


	/**
	 * Performs a scheduled automation.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id Automation ID
	 */
	protected static function do_scheduled_automation( $automation_id ) {

		if ( $automation = Automation_Factory::get_automation( $automation_id ) ) {

			if ( ! $automation->is_enabled() ) {
				return;
			}

			try {

				wc_customer_order_csv_export()->get_export_handler_instance()->start_export_from_automation( $automation );

			} catch ( Framework\SV_WC_Plugin_Exception $e ) {

				wc_customer_order_csv_export()->log( sprintf( 'Scheduled automated export failed: %s', $e->getMessage() ) );
			}
		}
	}


	/**
	 * Handles triggering exports on order status change.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id Order ID
	 */
	public static function auto_export_order( $order_id ) {

		$export_handler = wc_customer_order_csv_export()->get_export_handler_instance();
		$order          = wc_get_order( $order_id );

		if ( $order instanceof \WC_Order ) {

			// find enabled automations that are status based only
			$args = [
				'enabled' => true,
				'action'  => 'immediate',
			];

			foreach ( Automation_Factory::get_automations( $args ) as $automation ) {
				$export_handler->maybe_start_export_for_order( $order, $automation );
			}
		}
	}


	/**
	 * Schedules an automation.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id Automation ID
	 * @param Automation $automation automation object
	 */
	public static function schedule_automation( $automation_id, $automation ) {

		if ( $automation->is_interval_based() && $automation->get_start() ) {

			$start    = $automation->get_start()->getTimestamp();
			$interval = $automation->get_interval();

			if ( ! self::is_automation_scheduled( $automation_id ) ) {
				as_schedule_recurring_action( $start, $interval, self::$automation_hook, [ 'id' => $automation_id ] );
			}
		}
	}


	/**
	 * Updates an automation's schedule.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id Automation ID
	 * @param Automation $automation automation object
	 */
	public static function update_automation_schedule( $automation_id, $automation ) {

		if ( self::is_automation_scheduled( $automation_id ) ) {
			self::unschedule_automation( $automation_id );
		}

		self::schedule_automation( $automation_id, $automation );
	}


	/**
	 * Unschedules an automation.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id Automation ID
	 */
	public static function unschedule_automation( $automation_id ) {

		as_unschedule_action( self::$automation_hook, [ 'id' => $automation_id ] );
	}


	/**
	 * Returns whether the automation is already scheduled or not.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id the automation ID
	 * @return bool
	 */
	public static function is_automation_scheduled( $automation_id ) {

		return (bool) self::get_next_scheduled_action( $automation_id );
	}


	/**
	 * Returns the next time the given automation is scheduled to fire.
	 *
	 * @since 5.0.0
	 *
	 * @param string $automation_id the automation ID
	 * @return int|false timestamp of next action, or false if not scheduled
	 */
	public static function get_next_scheduled_action( $automation_id ) {

		return as_next_scheduled_action( self::$automation_hook, [ 'id' => $automation_id ] );
	}


	/**
	 * Cleans up any expired exports.
	 *
	 * @since 5.0.0
	 */
	public static function cleanup_expired_exports() {

		wc_customer_order_csv_export()->get_export_handler_instance()->remove_expired_exports();
	}


	/**
	 * Validates scheduled automations.
	 *
	 * @since 5.0.0
	 */
	public static function validate_scheduled_automations() {

		$automations       = Automation_Factory::get_automations();
		$scheduled_actions = self::get_scheduled_actions();

		foreach ( $scheduled_actions as $scheduled_action ) {

			$automation_id = self::get_action_automation_id( $scheduled_action );

			if ( isset( $automations[ $automation_id ] ) ) {

				if ( ! self::is_scheduled_action_valid( $scheduled_action ) ) {

					// automation exists but doesn't match the schedule -- update the schedule
					self::update_automation_schedule( $automation_id, $automations[ $automation_id ] );
				}

				// automation exists and matches the schedule -- move along, nothing to see here
				unset( $automations[ $automation_id ] );

			} else {

				// no automation found with this ID -- unschedule
				self::unschedule_automation( $automation_id );
			}
		}

		foreach ( $automations as $automation ) {

			if ( $automation->is_interval_based() ) {

				// schedule any interval-based automations that are left
				self::schedule_automation( $automation->get_id(), $automation );
			}
		}
	}


	/**
	 * Validates a single scheduled action.
	 *
	 * @since 5.0.0
	 *
	 * @param \ActionScheduler_Action $scheduled_action scheduled action instance
	 * @return bool
	 */
	public static function is_scheduled_action_valid( $scheduled_action ) {

		$automation = self::get_action_automation( $scheduled_action );

		return    $automation instanceof Automation
		       && $automation->is_interval_based()
		       && ( $schedule = $scheduled_action->get_schedule() )
		       && $schedule instanceof \ActionScheduler_IntervalSchedule
		       && $schedule->interval_in_seconds() === $automation->get_interval();
	}


	/**
	 * Gets all the upcoming scheduled actions for export automations.
	 *
	 * @since 5.0.0
	 *
	 * @return \ActionScheduler_Action[]
	 */
	public static function get_scheduled_actions() {

		return as_get_scheduled_actions( [
			'per_page' => -1,
			'hook'     => self::$automation_hook,
			'status'   => \ActionScheduler_Store::STATUS_PENDING
		] );
	}


	/**
	 * Gets the instantiated Automation for a given scheduled action.
	 *
	 * @since 5.0.0
	 *
	 * @param \ActionScheduler_Action $scheduled_action
	 * @return Automation|null Automation instance or null if not found
	 */
	public static function get_action_automation( $scheduled_action ) {

		return Automation_Factory::get_automation( self::get_action_automation_id( $scheduled_action ) );
	}


	/**
	 * Gets the automation ID from the arguments of a scheduled action.
	 *
	 * @since 5.0.0
	 *
	 * @param \ActionScheduler_Action $scheduled_action
	 * @return string
	 */
	public static function get_action_automation_id( $scheduled_action ) {

		$args = $scheduled_action->get_args();

		return isset( $args['id'] ) ? $args['id'] : '';
	}


	/**
	 * Determines if there is a cleanup task scheduled already.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	protected static function cleanup_is_scheduled() {

		return (bool) as_next_scheduled_action( self::$cleanup_hook );
	}


	/**
	 * Schedules a cleanup task.
	 *
	 * In 5.0.1 visibility changed from protected to public.
	 *
	 * @since 5.0.0
	 */
	public static function schedule_cleanup() {

		if ( self::cleanup_is_scheduled() ) {
			return;
		}

		/**
		 * Filters how often a cleanup of old exports should be done.
		 *
		 * @since 5.0.0
		 *
		 * @param int $interval number of seconds between cleanups (defaults to 24 hours)
		 */
		$cleanup_interval = absint( apply_filters( 'wc_customer_order_export_cleanup_interval', 24 * HOUR_IN_SECONDS ) );

		as_schedule_recurring_action( strtotime( '+5 minutes' ), $cleanup_interval, self::$cleanup_hook );
	}


	/**
	 * Determines if there is a validation task scheduled already.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	protected static function validation_is_scheduled() {

		return (bool) as_next_scheduled_action( self::$validate_hook );
	}


	/**
	 * Schedules a validation task.
	 *
	 * In 5.0.1 visibility changed from protected to public
	 *
	 * @since 5.0.0
	 */
	public static function schedule_validation() {

		if ( self::validation_is_scheduled() ) {
			return;
		}

		/**
		 * Filters how often a validation of scheduled actions and automations should be done.
		 *
		 * @since 5.0.0
		 *
		 * @param int $interval number of seconds between validation (defaults to 24 hours)
		 */
		$validation_interval = absint( apply_filters( 'wc_customer_order_export_schedule_validation_interval', 24 * HOUR_IN_SECONDS ) );

		as_schedule_recurring_action( strtotime( '+5 minutes' ), $validation_interval, self::$validate_hook );
	}


	/**
	 * Clears all scheduled actions used in the plugin.
	 *
	 * @since 5.0.0
	 */
	public static function clear_scheduled_actions() {

		as_unschedule_all_actions( self::$validate_hook );
		as_unschedule_all_actions( self::$cleanup_hook );
		as_unschedule_all_actions( self::$automation_hook );
	}


}
