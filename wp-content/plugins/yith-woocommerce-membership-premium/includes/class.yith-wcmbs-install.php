<?php
defined( 'YITH_WCMBS' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Install' ) ) {
	/**
	 * Class YITH_WCMBS_Install
	 *
	 * @since 1.4.0
	 */
	class YITH_WCMBS_Install {
		/**
		 * Database update callbacks.
		 *
		 * @var string[][]
		 */
		private static $db_updates = array(
			'1.4.0'  => array(
				'yith_wcmbs_update_140_int_to_string_array_meta',
				'yith_wcmbs_update_140_db_version',
			),
			'1.18.0' => array(
				'yith_wcmbs_update_1_18_0_clear_scheduled_events',
				'yith_wcmbs_update_1_18_0_db_version',
			),
		);

		/**
		 * Init
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
			add_action( 'yith_wcmbs_run_update_callback', array( __CLASS__, 'run_update_callback' ) );
		}

		/**
		 * Check the plugin version and run the updater is required.
		 * This check is done on all requests and runs if the versions do not match.
		 */
		public static function check_version() {
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'yith_woocommerce_membership_version', '1.0.0' ), YITH_WCMBS_VERSION, '<' ) ) {
				self::install();
				do_action( 'yith_wcmbs_updated' );
			}
		}

		/**
		 * Get list of DB update callbacks.
		 *
		 * @return array
		 */
		public static function get_db_update_callbacks() {
			return self::$db_updates;
		}

		/**
		 * Install WC.
		 */
		public static function install() {
			// Check if we are not already running this routine.
			if ( 'yes' === get_transient( 'yith_wcmbs_installing' ) ) {
				return;
			}

			// If we made it till here nothing is running yet, lets set the transient now.
			set_transient( 'yith_wcmbs_installing', 'yes', MINUTE_IN_SECONDS * 10 );
			if ( ! defined( 'YITH_WCMBS_INSTALLING' ) ) {
				define( 'YITH_WCMBS_INSTALLING', true );
			}

			self::create_tables();

			YITH_WCMBS_Legacy_Elements::check_for_legacy_elements();
			YITH_WCMBS_Endpoints::install();
			YITH_WCMBS_Post_Types::install();
			YITH_WCMBS_Post_Types::add_capabilities();

			self::update_version();
			self::maybe_update_db_version();

			delete_transient( 'yith_wcmbs_installing' );

			do_action( 'yith_wcmbs_installed' );
		}

		/**
		 * Update version to current.
		 */
		private static function update_version() {
			delete_option( 'yith_woocommerce_membership_version' );
			add_option( 'yith_woocommerce_membership_version', YITH_WCMBS_VERSION );
		}


		/**
		 * The DB needs to be updated?
		 *
		 * @return bool
		 */
		public static function needs_db_update() {
			$current_db_version = get_option( 'yith_wcmbs_db_version', null );
			$updates            = self::get_db_update_callbacks();
			$update_versions    = array_keys( $updates );
			usort( $update_versions, 'version_compare' );

			return ! is_null( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
		}

		/**
		 * Update DB version to current.
		 *
		 * @param string|null $version New DB version or null.
		 */
		public static function update_db_version( $version = null ) {
			delete_option( 'yith_wcmbs_db_version' );
			add_option( 'yith_wcmbs_db_version', is_null( $version ) ? YITH_WCMBS_VERSION : $version );
		}

		/**
		 * Maybe update db
		 */
		private static function maybe_update_db_version() {
			if ( self::needs_db_update() ) {
				self::update();
			} else {
				self::update_db_version();
			}
		}

		/**
		 * Push all needed DB updates to the queue for processing.
		 */
		private static function update() {
			$current_db_version = get_option( 'yith_wcmbs_db_version' );
			$loop               = 0;

			foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
				if ( version_compare( $current_db_version, $version, '<' ) ) {
					foreach ( $update_callbacks as $update_callback ) {
						WC()->queue()->schedule_single(
							time() + $loop,
							'yith_wcmbs_run_update_callback',
							array(
								'update_callback' => $update_callback,
							),
							'yith-wcmbs-db-updates'
						);
						$loop ++;
					}
				}
			}
		}

		/**
		 * Run an update callback when triggered by ActionScheduler.
		 *
		 * @param string $callback Callback name.
		 */
		public static function run_update_callback( $callback ) {
			include_once YITH_WCMBS_INCLUDES_PATH . '/functions.yith-wcmbs-update.php';

			if ( is_callable( $callback ) ) {
				self::run_update_callback_start( $callback );
				$result = (bool) call_user_func( $callback );
				self::run_update_callback_end( $callback, $result );
			}
		}

		/**
		 * Triggered when a callback will run.
		 *
		 * @param string $callback Callback name.
		 */
		protected static function run_update_callback_start( $callback ) {
			if ( ! defined( 'YITH_WCMBS_UPDATING' ) ) {
				define( 'YITH_WCMBS_UPDATING', true );
			}
		}

		/**
		 * Triggered when a callback has ran.
		 *
		 * @param string $callback Callback name.
		 * @param bool   $result   Return value from callback. Non-false need to run again.
		 */
		protected static function run_update_callback_end( $callback, $result ) {
			if ( $result ) {
				WC()->queue()->add(
					'yith_wcmbs_run_update_callback',
					array(
						'update_callback' => $callback,
					),
					'yith-wcmbs-db-updates'
				);
			}
		}

		private static function create_tables() {
			global $wpdb;

			$wpdb->hide_errors();
			$table_name      = $wpdb->prefix . 'yith_wcmbs_downloads_log';
			$charset_collate = $wpdb->get_charset_collate();

			$sql
				= "CREATE TABLE $table_name (
                    `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `type` varchar(255) NOT NULL DEFAULT '',
                    `product_id` bigint(20) NOT NULL,
                    `user_id` bigint(20) NOT NULL,
                    `user_ip_address` VARCHAR(100) NULL DEFAULT '',
                    `timestamp_date` datetime NOT NULL,
                    PRIMARY KEY (id)
                    ) $charset_collate;";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql );
		}
	}
}