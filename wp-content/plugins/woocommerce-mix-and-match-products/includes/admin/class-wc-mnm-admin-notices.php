<?php
/**
 * Admin Notices
 *
 * @author   Kathy Darling
 * @category Admin
 * @package  WooCommerce Mix and Match Products/Admin
 * @since    1.2.0
 * @version  1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Admin_Notices Class.
 *
 * Handle the addition/display of admin notices.
 */
class WC_MNM_Admin_Notices {

	/**
	 * Metabox Notices.
	 * 
	 * @var array
	 */
	public static $meta_box_notices = array();

	/**
	 * Admin Notices.
	 * 
	 * @var array
	 */
	public static $admin_notices    = array();

	/**
	 * Maintenance Notices.
	 * 
	 * @var array
	 */
	public static $maintenance_notices = array();

	/**
	 * Array of maintenance notice types - name => callback.
	 * 
	 * @var array
	 */
	private static $maintenance_notice_types = array(
		'update' => 'update_notice'
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$maintenance_notices = get_option( 'wc_mnm_maintenance_notices', array() );

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ) );
		// Show maintenance notices.
		add_action( 'admin_print_styles', array( __CLASS__, 'hook_maintenance_notices' ) );
		// Act upon clicking on a 'dismiss notice' link.
		add_action( 'wp_loaded', array( __CLASS__, 'dismiss_notice_handler' ) );
	}

	/**
	 * Add a notice/error.
	 *
	 * @param  string   $text
	 * @param  mixed    $args
	 * @param  bool  $save_notice
	 */
	public static function add_notice( $text, $args, $save_notice = false ) {

		if ( is_array( $args ) ) {
			$type          = $args[ 'type' ];
			$dismiss_class = isset( $args[ 'dismiss_class' ] ) ? $args[ 'dismiss_class' ] : false;
		} else {
			$type          = $args;
			$dismiss_class = false;
		}

		$notice = array(
			'type'          => $type,
			'content'       => $text,
			'dismiss_class' => $dismiss_class
		);

		if ( $save_notice ) {
			self::$meta_box_notices[] = $notice;
		} else {
			self::$admin_notices[] = $notice;
		}
	}

	/**
	 * Checks if a maintenance notice is visible.
	 *
	 * @since  1.10.0
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_maintenance_notice_visible( $notice_name ) {
		return in_array( $notice_name, self::$maintenance_notices );
	}

	/**
	 * Checks if a dismissible notice has been dismissed in the past.
	 *
	 * @since  1.10.0
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_dismissible_notice_dismissed( $notice_name ) {
		return in_array( $notice_name, self::$dismissed_notices );
	}

	/**
	 * Save errors to an option.
	 */
	public static function save_notices() {
		update_option( 'wc_mnm_meta_box_notices', self::$meta_box_notices );
		update_option( 'wc_mnm_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = maybe_unserialize( get_option( 'wc_mnm_meta_box_notices', array() ) );
		$notices       = $saved_notices + self::$admin_notices;

		if ( ! empty( $notices ) ) {

			foreach ( $notices as $notice ) {

				$dismiss_class = $notice[ 'dismiss_class' ] ? $notice[ 'dismiss_class' ] . ' is-persistent' : 'is-dismissible';

				echo '<div class="wc-mnm-notice notice-' . $notice[ 'type' ] . ' notice ' . $dismiss_class . '">';

				if ( $notice[ 'dismiss_class' ] ) {
					$dismiss_url = esc_url( wp_nonce_url( add_query_arg( 'dismiss_wc_mnm_notice', $notice[ 'dismiss_class' ] ), 'wc_mnm_dismiss_notice_nonce', '_wc_mnm_admin_nonce' ) );
					echo '<a class="wc-mnm-dismiss-notice notice-dismiss" href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-mix-and-match-products' ) . '</a>';
				}

				echo '<p>' . wp_kses_post( $notice[ 'content' ] ) . '</p>';
				echo '</div>';
			}

			// Clear.
			delete_option( 'wc_mnm_meta_box_notices' );
		}
	}

	/**
	 * Show maintenance notices.
	 */
	public static function hook_maintenance_notices() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		foreach ( self::$maintenance_notice_types as $type => $callback ) {
			if ( in_array( $type, self::$maintenance_notices ) ) {
				call_user_func( array( __CLASS__, $callback ) );
			}
		}
	}

	/**
	 * Add a maintenance notice to be displayed.
	 *
	 * @param  string  $notice_name
	 */
	public static function add_maintenance_notice( $notice_name ) {

		// Add if not already there.
		if ( ! self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_merge( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Remove a maintenance notice.
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_maintenance_notice( $notice_name ) {

		// Remove if there.
		if ( self::is_maintenance_notice_visible( $notice_name ) ) {
			self::$maintenance_notices = array_diff( self::$maintenance_notices, array( $notice_name ) );
			return true;
		}

		return false;
	}

	/**
	 * Add 'update' maintenance notice.
	 *
	 * @since  1.10.0
	 */
	public static function update_notice() {

		if ( ! class_exists( 'WC_MNM_Install' ) ) {
			return;
		}

		if ( WC_MNM_Install::is_update_pending() ) {

			$status = '';

		// Show notice to indicate that an update is in progress.
		if ( WC_MNM_Install::is_update_process_running() || WC_MNM_Install::is_update_queued() ) {

			$status = __( 'Your database is being updated in the background.', 'woocommerce-mix-and-match-products' );

			// Check if the update process is running.
			if ( false === WC_MNM_Install::is_update_process_running() ) {
				$status .= self::get_force_update_prompt();
			}

			// Show a prompt to update.
			} elseif ( false === WC_MNM_Install::auto_update_enabled() && false === WC_MNM_Install::is_update_incomplete() ) {

				$status  = __( 'Your database needs to be updated to the latest version.', 'woocommerce-mix-and-match-products' );
				$status .= self::get_trigger_update_prompt();

			} elseif ( WC_MNM_Install::is_update_incomplete() ) {

				$status  = __( 'Database update incomplete.', 'woocommerce-mix-and-match-products' );
				$status .= self::get_failed_update_prompt();
			}

			if ( $status ) {
				$notice = '<strong>' . __( 'WooCommerce Mix and Match Products Data Update', 'woocommerce-mix-and-match-products' ) . '</strong> &#8211; ' . $status;
				self::add_notice( $notice, 'info' );
			}
	
		// Show persistent notice to indicate that the updating process is complete.
		} else {
			$notice         = __( 'WooCommerce Mix and Match Products data update complete.', 'woocommerce-mix-and-match-products' );
			self::add_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'update' ) );
		}

	}

	/**
	 * Returns a "trigger update" notice component.
	 *
	 * @since  1.10.0
	 *
	 * @return string
	 */
	private static function get_trigger_update_prompt() {
		$update_url    = esc_url( wp_nonce_url( add_query_arg( 'trigger_wc_mnm_db_update', true, admin_url() ), 'wc_mnm_trigger_db_update_nonce', '_wc_mnm_admin_nonce' ) );
		$update_prompt = '<p><a href="' . $update_url . '" class="wc-pb-update-now button-primary">' . __( 'Run the updater', 'woocommerce-mix-and-match-products' ) . '</a></p>';
		return $update_prompt;
	}

	/**
	 * Returns a "force update" notice component.
	 *
	 * @since  1.10.0
	 *
	 * @return string
	 */
	private static function get_force_update_prompt() {

		$fallback_prompt = '';
		$update_runtime  = get_option( 'wc_mnm_update_init', 0 );

		// Wait for at least 30 seconds.
		if ( gmdate( 'U' ) - $update_runtime > 30 ) {
			// Perhaps the upgrade process failed to start?
			$fallback_url    = esc_url( wp_nonce_url( add_query_arg( 'force_wc_mnm_db_update', true, admin_url() ), 'wc_mnm_force_db_update_nonce', '_wc_mnm_admin_nonce' ) );
			$fallback_link   = '<a href="' . $fallback_url . '">' . __( 'run the update process manually', 'woocommerce-mix-and-match-products' ) . '</a>';
			$fallback_prompt = '<br/><em>' . sprintf( __( '&hellip;Taking a while? You may need to %s.', 'woocommerce-mix-and-match-products' ), $fallback_link ) . '</em>';
		}

		return $fallback_prompt;
	}

	/**
	 * Returns a "failed update" notice component.
	 *
	 * @since  1.10.0
	 *
	 * @return string
	 */
	private static function get_failed_update_prompt() {

		$support_url    = esc_url( WC_MNM_SUPPORT_URL );
		$support_link   = '<a href="' . $support_url . '">' . __( 'get in touch with us', 'woocommerce-mix-and-match-products' ) . '</a>';
		/* translators: %s is support link. */
		$support_prompt = '<br/><em>' . sprintf( __( 'If this message persists, please restore your database from a backup, or %s.', 'woocommerce-mix-and-match-products' ), $support_link ) . '</em>';

		return $support_prompt;
	}

	/**
	 * Dismisses a notice.
	 *
	 * @since  1.10.0
	 *
	 * @param  string  $notice
	 */
	public static function dismiss_notice( $notice ) {
		if ( isset( self::$maintenance_notice_types[ $notice ] ) ) {
			return self::remove_maintenance_notice( $notice );
		} else {
			return self::remove_dismissible_notice( $notice );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Act upon clicking on a 'dismiss notice' link.
	 *
	 * @deprecated 1.10.0
	 */
	public static function dismiss_notice_handler() {
		if ( isset( $_GET[ 'dismiss_wc_mnm_notice' ] ) && isset( $_GET[ '_wc_mnm_admin_nonce' ] ) ) {
			if ( ! wp_verify_nonce( $_GET[ '_wc_mnm_admin_nonce' ], 'wc_mnm_dismiss_notice_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-mix-and-match-products' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'You do not have permission to dismiss this notice.', 'woocommerce-mix-and-match-products' ) );
			}

			$notice = sanitize_text_field( $_GET[ 'dismiss_wc_mnm_notice' ] );
			self::dismiss_notice( $notice );
		}
	}

	/**
	 * Add 'updating' maintenance notice.
	 *
	 * @deprecated 1.10.0
	 */
	public static function updating_notice() {
		return self::update_notice();
	}

}

WC_MNM_Admin_Notices::init();
