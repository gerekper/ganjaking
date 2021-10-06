<?php
/**
 * WooCommerce Smart Coupon DB update.
 *
 * @author      StoreApps
 * @since       4.28.0
 * @version     1.0.0
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Background_Upgrade' ) ) {
	/**
	 * Class for WooCommerce Smart Coupons database update.
	 */
	class WC_SC_Background_Upgrade {

		/**
		 * Number of row to fetch once a query run.
		 *
		 * (default value: 100)
		 *
		 * @var int
		 * @access protected
		 */
		protected $row_limit = 100;

		/**
		 * Action name.
		 *
		 * @var string
		 * @access protected
		 */
		protected $action = '';

		/**
		 * Variable to hold instance of WC_SC_Background_Upgrade
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Get single instance of WC_SC_Background_Upgrade
		 *
		 * @return WC_SC_Background_Upgrade Singleton object of WC_SC_Background_Upgrade
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Example_Background_Processing constructor.
		 */
		private function __construct() {
			$this->action = 'wc_db_upgrade';
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'process_handler' ) );
			add_action( 'init', array( $this, 'clear_all_process' ) );
			add_action( 'action_scheduler_failed_action', array( $this, 'restart_failed_action' ) );
		}

		/**
		 * Init
		 */
		public function init() {
			global $woocommerce_smart_coupon;
			// Get list of db updates.
			$updates = $this->get_updates();
			if ( ! empty( $updates ) ) {
				foreach ( $updates as $update ) {
					// Break if version is empty.
					if ( empty( $update['version'] ) ) {
						break;
					}

					$version       = $update['version'];
					$update_status = $this->get_status( $version );
					if ( version_compare( $woocommerce_smart_coupon->get_smart_coupons_version(), $version, '>=' ) && ( false === $update_status ) ) {
						// Set db update status to pending.
						$this->set_status( $version, 'pending' );
					}
					$handler = isset( $update['cron_handler'] ) ? $update['cron_handler'] : '';
					$this->register_scheduler( $handler );
				}
			}
		}

		/**
		 * Process handler
		 */
		public function process_handler() {
			if ( ! isset( $_GET['wc_sc_update'] ) || ! isset( $_GET['wc_sc_db_update_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( wc_clean( wp_unslash( $_GET['wc_sc_db_update_nonce'] ) ), 'wc_sc_db_process' ) ) { // phpcs:ignore
				return;
			}

			$this->handle_all( wc_clean( wp_unslash( $_GET['wc_sc_update'] ) ) ); // phpcs:ignore
		}

		/**
		 * Get list of db updates.
		 */
		public function get_updates() {
			return array(
				array(
					'version'         => '4.28.0', // Minimum plugin version to do the action.
					'get_row_handler' => array( __CLASS__, 'get_applied_coupon_profile_options' ), // get data.
					'cron_handler'    => 'wcsc_move_applied_coupon_options_to_transient', // define crone handler which should be in this class.
				),
			);
		}

		/**
		 * Register action schedulers for db update.
		 *
		 * @param string $handler Handler name.
		 */
		public function register_scheduler( $handler = '' ) {
			if ( ! empty( $handler ) && is_callable( array( $this, $handler ) ) ) {
				add_action( $handler, array( $this, $handler ) );
			}
		}

		/**
		 * Handle all updates.
		 *
		 * @param string $current_version Plugin version.
		 */
		protected function handle_all( $current_version = '0' ) {
			if ( empty( $current_version ) ) {
				return;
			}
			do_action( 'wc_sc_start_background_update', $current_version );
			$updates = $this->get_updates();
			if ( is_array( $updates ) && ! empty( $updates ) ) {
				foreach ( $updates as $update ) {
					// Compare version for db updates.
					if ( ! empty( $update['version'] ) && version_compare( $current_version, $update['version'], '>=' ) ) {
						$cron_handler = isset( $update['cron_handler'] ) ? $update['cron_handler'] : '';
						if ( ! empty( $cron_handler ) && is_callable( array( $this, $cron_handler ) ) ) {
							$row_handler = isset( $update['get_row_handler'] ) ? $update['get_row_handler'] : '';
							$this->process( $update['version'], $cron_handler, $row_handler );
						}
					}
				}
			}
		}

		/**
		 * Handle the process.
		 *
		 * @param string $version       Version number.
		 * @param string $cron_handler  Cron handler action name.
		 * @param string $row_handler   Fetch data handler name.
		 */
		private function process( $version = '', $cron_handler = '', $row_handler = '' ) {
			$rows = ! empty( $row_handler ) && is_callable( $row_handler ) ? call_user_func( $row_handler ) : '';
			if ( ! empty( $rows ) && is_array( $rows ) ) {
				// Start the process if the status is pending.
				if ( 'pending' === $this->get_status( $version ) ) {
					if ( function_exists( 'as_enqueue_async_action' ) ) {
						$this->set_status( $version, 'processing' );
						as_enqueue_async_action( $cron_handler );
					}
				}
			} else {
				// Set status to `completed` if the data is empty.
				$this->set_status( $version, 'completed' );
			}
		}

		/**
		 * Clear all update process.
		 */
		public function clear_all_process() {
			$updates = $this->get_updates();
			foreach ( $updates as $update ) {
				$version     = isset( $update['version'] ) ? $update['version'] : '';
				$row_handler = isset( $update['get_row_handler'] ) ? $update['get_row_handler'] : '';
				$status      = $this->get_status( $version );
				if ( false === $status || 'done' === $status ) {
					return;
				}
				$rows = ! empty( $row_handler ) && is_callable( $row_handler ) ? call_user_func( $row_handler ) : '';
				if ( 'processing' === $status && empty( $rows ) ) {
					do_action( 'wc_sc_background_update_completed', $version );
					$this->set_status( $version, 'completed' );
				}
			}
		}

		/**
		 * Callback Method for move options to transient.
		 *
		 * @return void
		 */
		public function wcsc_move_applied_coupon_options_to_transient() {
			$options = $this->get_applied_coupon_profile_options();
			// Check if coupons are not empty.
			if ( ! empty( $options ) && is_array( $options ) ) {

				$start_time = time();
				$loop       = 1;
				foreach ( $options as $option ) {
					// disable auto apply.
					$this->move_option_to_transient( $option );

					if ( $this->loop_exceeded( $loop ) || $this->time_exceeded( $start_time ) || $this->memory_exceeded() ) {
						// Update auto apply coupon id list.
						if ( function_exists( 'as_enqueue_async_action' ) ) {
							as_enqueue_async_action( __FUNCTION__ );
						}
						break;
					}
					$loop++;
				}
			}
		}

		/**
		 * Restart scheduler after one minute if it fails
		 *
		 * @param  array $action_id id of failed action.
		 */
		public function restart_failed_action( $action_id = 0 ) {

			if ( empty( $action_id ) || ! class_exists( 'ActionScheduler' ) || ! is_callable( array( 'ActionScheduler', 'store' ) ) || ! function_exists( 'as_enqueue_async_action' ) ) {
				return;
			}

			$action      = ActionScheduler::store()->fetch_action( $action_id );
			$action_hook = $action->get_hook();

			$updates = $this->get_updates();
			if ( ! empty( $updates ) ) {
				foreach ( $updates as $update ) {
					if ( ! empty( $update['version'] ) && ! empty( $update['cron_handler'] ) ) {
						if ( $action_hook === $update['cron_handler'] ) {
							$this->set_status( $update['version'], 'processing' );
							as_enqueue_async_action( $update['cron_handler'] );
						}
					}
				}
			}
		}

		/**
		 * Method to update the status of db upgrade.
		 *
		 * @param string $version wcsc db version.
		 * @param string $status  status.
		 *
		 * @return bool.
		 */
		public function set_status( $version = '', $status = '' ) {
			if ( ! empty( $version ) && ! empty( $status ) ) {
				$db_status             = get_option( 'sc_wc_db_update_status', array() );
				$db_status[ $version ] = $status;
				return update_option( 'sc_wc_db_update_status', $db_status );
			}
			return false;
		}

		/**
		 * Method to get the status of db upgrade.
		 *
		 * @param string $version wcsc db version.
		 *
		 * @return bool.
		 */
		public function get_status( $version = '' ) {
			if ( ! empty( $version ) ) {
				$db_status = get_option( 'sc_wc_db_update_status', array() );
				return ( ! empty( $db_status ) && isset( $db_status[ $version ] ) ) ? $db_status[ $version ] : false;
			}
			return false;
		}

		/**
		 * Loop exceeded
		 *
		 * Ensures the batch process never exceeds to handle the given limit of row.
		 *
		 * @param int $loop Number of loop done.
		 * @return bool
		 */
		public function loop_exceeded( $loop = 0 ) {
			return $loop > apply_filters( $this->action . '_default_row_limit', $this->row_limit );
		}

		/**
		 * Memory exceeded
		 *
		 * Ensures the batch process never exceeds 90%
		 * of the maximum WordPress memory.
		 *
		 * @return bool
		 */
		protected function memory_exceeded() {
			$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
			$current_memory = memory_get_usage( true );

			if ( $current_memory >= $memory_limit ) {
				return true;
			}

			return false;
		}

		/**
		 * Get memory limit.
		 *
		 * @return int
		 */
		protected function get_memory_limit() {
			if ( function_exists( 'ini_get' ) ) {
				$memory_limit = ini_get( 'memory_limit' );
			} else {
				// Sensible default.
				$memory_limit = '128M';
			}

			if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
				// Unlimited, set to 32GB.
				$memory_limit = '32G';
			}

			return wp_convert_hr_to_bytes( $memory_limit );
		}

		/**
		 * Time exceeded.
		 *
		 * Ensures the batch never exceeds a sensible time limit.
		 * A timeout limit of 30s is common on shared hosting.
		 *
		 * @param string $start_time start timestamp.
		 * @return bool
		 */
		protected function time_exceeded( $start_time = '' ) {

			$finish = $start_time + apply_filters( $this->action . '_default_time_limit', 20 ); // 20 seconds
			$return = false;

			if ( time() >= $finish ) {
				$return = true;
			}

			return apply_filters( $this->action . '_time_exceeded', $return );
		}

		/**
		 * Get `applied_coupon_profile` options names and values
		 *
		 * @return array
		 */
		public function get_applied_coupon_profile_options() {
			global $wpdb;
			$option_name = 'sc_applied_coupon_profile_%';
			$options = $wpdb->get_results( // @codingStandardsIgnoreLine
				$wpdb->prepare(
					"SELECT option_name, option_value
			            FROM $wpdb->options
			            WHERE option_name LIKE %s",
					$option_name
				),
				ARRAY_A
			);
			return $options;
		}

		/**
		 * Method to transfer single options to transient.
		 *
		 * @param array $option Array of option name and value.
		 * @return void
		 */
		protected function move_option_to_transient( $option = array() ) {
			if ( isset( $option['option_name'] ) && isset( $option['option_value'] ) ) {

				// Add new transient with option name.
				$move = set_transient(
					$option['option_name'],
					maybe_unserialize( $option['option_value'] ),
					apply_filters( 'wc_sc_applied_coupon_by_url_expire_time', MONTH_IN_SECONDS )
				);

				if ( true === $move ) {
					// Delete the option if option is successfully moved.
					delete_option( $option['option_name'] );
				}
			}
		}

	} // End class
} // End class exists check

WC_SC_Background_Upgrade::get_instance();
