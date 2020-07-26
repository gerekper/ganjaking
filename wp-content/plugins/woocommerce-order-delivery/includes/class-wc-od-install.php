<?php
/**
 * Installation related functions and actions.
 *
 * Inspired in the WC_Install class.
 *
 * @package WC_OD
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Install' ) ) {
	/**
	 * WC_OD_Install class.
	 */
	class WC_OD_Install {

		/**
		 * Database updates that need to be run per version.
		 *
		 * @since 1.4.0
		 * @var array
		 */
		private static $db_updates = array(
			// The migration 1.4.0 has been removed because it's the same as the 1.4.1. It avoids to execute the code twice for new installs.
			'1.4.1' => array(
				'wc_od_update_141_shipping_dates',
				'wc_od_update_141_db_version',
			),
			'1.5.0' => array(
				'wc_od_update_150_settings_bool_values_to_string',
				'wc_od_update_150_subscriptions_bool_values_to_string',
				'wc_od_update_150_delivery_days_setting',
				'wc_od_update_150_db_version',
			),
			'1.6.0' => array(
				'wc_od_update_160_rename_delivery_date_field_setting',
				'wc_od_update_160_db_version',
			),
		);

		/**
		 * Background update class.
		 *
		 * @since 1.4.0
		 * @var object
		 */
		private static $background_updater;

		/**
		 * Init installation.
		 *
		 * @since 1.2.0
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
			add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
			add_action( 'init', array( __CLASS__, 'add_endpoints' ) );
			add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
			add_action( 'admin_init', array( __CLASS__, 'add_notices' ), 20 );
			add_action( 'wc_od_updater_complete', array( __CLASS__, 'updated' ) );
			add_action( 'wc_od_purge_expired_events', array( __CLASS__, 'purge_expired_events' ) );
		}

		/**
		 * Get the database updates.
		 *
		 * @since 1.4.0
		 *
		 * @return array
		 */
		public static function get_db_updates() {
			return self::$db_updates;
		}

		/**
		 * Init background updates.
		 *
		 * @since 1.4.0
		 */
		public static function init_background_updater() {
			include_once dirname( __FILE__ ) . '/class-wc-od-background-updater.php';
			self::$background_updater = new WC_OD_Background_Updater();
		}

		/**
		 * Check the plugin version and run the updater is necessary.
		 *
		 * This check is done on all requests and runs if the versions do not match.
		 *
		 * @since 1.4.0
		 */
		public static function check_version() {
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wc_od_version' ), WC_OD_VERSION, '<' ) ) {
				self::install();
				do_action( 'wc_od_updated' );
			}
		}

		/**
		 * Install actions when an update button is clicked within the admin area.
		 *
		 * @since 1.4.0
		 */
		public static function install_actions() {
			if ( ! empty( $_GET['do_update_wc_od'] ) ) {
				check_admin_referer( 'wc_od_db_update', 'wc_od_db_update_nonce' );
				self::update();
			}

			if ( ! empty( $_GET['force_update_wc_od'] ) ) {
				check_admin_referer( 'wc_od_force_db_update', 'wc_od_force_db_update_nonce' );
				$blog_id = get_current_blog_id();
				do_action( 'wp_' . $blog_id . '_wc_od_updater_cron' );
				wp_safe_redirect( wc_od_get_settings_url() );
				exit;
			}
		}

		/**
		 * Removes any notices added to admin.
		 *
		 * @since 1.6.0
		 */
		private static function remove_notices() {
			include_once dirname( __FILE__ ) . '/admin/class-wc-od-admin-notices.php';

			// Remove not dismissed notice from a previous update.
			WC_OD_Admin_Notices::remove_notice( 'wc_od_updated' );
		}

		/**
		 * Add installer/updater notices + styles if needed.
		 *
		 * @since 1.4.0
		 */
		public static function add_notices() {
			if ( WC_OD_Admin_Notices::has_notice( 'wc_od_updated' ) ) {
				return;
			}

			self::update_notice();
		}

		/**
		 * Adds the update notices.
		 *
		 * @since 1.4.0
		 */
		public static function update_notice() {
			if ( self::needs_db_update() ) {
				if ( self::$background_updater->is_updating() || ! empty( $_GET['do_update_wc_od'] ) ) { // WPCS: CSRF ok.
					WC_OD_Admin_Notices::add_notice( 'updating' );
				} else {
					WC_OD_Admin_Notices::add_notice( 'update' );
				}
			}
		}

		/**
		 * Init installation.
		 *
		 * @since 1.2.0
		 */
		public static function install() {
			if ( ! is_blog_installed() ) {
				return;
			}

			// Check if we are not already running the installation process.
			if ( 'yes' === get_transient( 'wc_od_installing' ) ) {
				return;
			}

			// Add transient to indicate that we are running the installation process.
			set_transient( 'wc_od_installing', 'yes', MINUTE_IN_SECONDS * 10 );

			self::remove_notices();
			self::add_cron_jobs();
			self::add_endpoints();
			self::update_version();
			self::maybe_update_db();

			// Installation finished.
			delete_transient( 'wc_od_installing' );

			flush_rewrite_rules();
		}

		/**
		 * Update database version to current.
		 *
		 * @since 1.4.0
		 *
		 * @param string|null $version Optional. The new database version. Plugin version by default.
		 */
		public static function update_db_version( $version = null ) {
			delete_option( 'wc_od_db_version' );
			add_option( 'wc_od_db_version', is_null( $version ) ? WC_OD_VERSION : $version );
		}

		/**
		 * Update the plugin version to current.
		 *
		 * @since 1.4.0
		 */
		private static function update_version() {
			delete_option( 'wc_od_version' );
			add_option( 'wc_od_version', WC_OD_VERSION );
		}

		/**
		 * Update the database if necessary.
		 *
		 * @since 1.4.0
		 */
		private static function maybe_update_db() {
			if ( ! self::needs_db_update() ) {
				self::update_db_version();
			}
		}

		/**
		 * Get if the database needs to be updated or not.
		 *
		 * @since 1.4.0
		 *
		 * @return bool
		 */
		private static function needs_db_update() {
			$needs_update = false;
			$db_version   = get_option( 'wc_od_db_version', null );
			$updates      = self::get_db_updates();

			// It's the first time we store the database version.
			if ( is_null( $db_version ) ) {
				// An older version of the plugin is installed.
				$needs_update = self::exists_delivery_dates();
			} elseif ( version_compare( $db_version, max( array_keys( $updates ) ), '<' ) ) {
				$needs_update = true;
			}

			return $needs_update;
		}

		/**
		 * Gets if there are orders with a delivery date.
		 *
		 * @since 1.4.0
		 *
		 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
		 *
		 * @return bool
		 */
		private static function exists_delivery_dates() {
			global $wpdb;

			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_delivery_date'" );

			return ( 0 < absint( $count ) );
		}

		/**
		 * Push all needed database updates to the queue for processing.
		 *
		 * @since 1.4.0
		 */
		private static function update() {
			$db_version    = get_option( 'wc_od_db_version' );
			$update_queued = false;

			foreach ( self::get_db_updates() as $version => $update_callbacks ) {
				if ( version_compare( $db_version, $version, '<' ) ) {
					foreach ( $update_callbacks as $update_callback ) {
						self::$background_updater->push_to_queue( $update_callback );
						$update_queued = true;
					}
				}
			}

			if ( $update_queued ) {
				self::$background_updater->save()->dispatch();
			}
		}

		/**
		 * Database updated.
		 *
		 * @since 1.6.0
		 */
		public static function updated() {
			self::update_db_version();

			WC_OD_Admin_Notices::add_notice(
				'wc_od_updated',
				_x( 'WooCommerce Order Delivery update complete. Thank you for updating to the latest version!', 'admin notice', 'woocommerce-order-delivery' )
			);
		}

		/**
		 * Register cron jobs.
		 *
		 * @since 1.2.0
		 */
		public static function add_cron_jobs() {
			wp_clear_scheduled_hook( 'wc_od_purge_expired_events' );
			wp_schedule_event( time(), 'monthly', 'wc_od_purge_expired_events' );
		}

		/**
		 * Register custom endpoints.
		 *
		 * @since 1.3.0
		 */
		public static function add_endpoints() {
			/**
			 * Fired to register additional endpoints.
			 *
			 * @since 1.3.0
			 */
			do_action( 'wc_od_install_add_endpoints' );
		}

		/**
		 * Adds custom links to the plugins page.
		 *
		 * @since 1.2.0
		 * @deprecated 1.6.0 Moved to WC_OD_Admin->plugin_action_links()
		 * @see WC_OD_Admin->plugin_action_links()
		 *
		 * @param array $links The plugin links.
		 * @return array The filtered plugin links.
		 */
		public static function plugin_action_links( $links ) {
			wc_deprecated_function( __METHOD__, '1.6.0', 'Moved to WC_OD_Admin->plugin_action_links()' );

			return $links;
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @since 1.2.0
		 * @deprecated 1.6.0 Moved to WC_OD_Admin->plugin_row_meta()
		 * @see WC_OD_Admin->plugin_row_meta()
		 *
		 * @param mixed $links Plugin Row Meta.
		 * @param mixed $file  Plugin Base file.
		 * @return array
		 */
		public static function plugin_row_meta( $links, $file ) {
			wc_deprecated_function( __METHOD__, '1.6.0', 'Moved to WC_OD_Admin->plugin_row_meta()' );

			return $links;
		}

		/**
		 * Deletes the expired events from the database to improve the performance.
		 *
		 * @since 1.2.0
		 */
		public static function purge_expired_events() {
			$types    = array( 'shipping', 'delivery' );
			$end_date = date( 'Y-m-d', strtotime( '-1 year', wc_od_get_local_date() ) );

			foreach ( $types as $type ) {
				$events  = WC_OD()->settings()->get_setting( "{$type}_events" );
				$expired = wc_od_get_events(
					array(
						'type'  => $type,
						'start' => '1970-01-01',
						'end'   => $end_date,
					)
				);

				if ( ! empty( $expired ) ) {
					$expired_ids  = wp_list_pluck( $expired, 'id' );
					$valid_events = array();

					foreach ( $events as $index => $event ) {
						if ( ! in_array( $event['id'], $expired_ids ) ) {
							$valid_events[ $event['id'] ] = $event;
						}
					}

					// Update the setting.
					WC_OD()->settings()->update_setting( "{$type}_events", $valid_events );
				}
			}
		}
	}
}

WC_OD_Install::init();
