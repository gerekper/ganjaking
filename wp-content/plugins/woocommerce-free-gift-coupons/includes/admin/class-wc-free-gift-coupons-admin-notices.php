<?php
/**
 * WC_Free_Gift_Coupons_Admin_Notices class
 *
 * @package  WooCommerce Free Gift Coupons
 * @since    2.0.0
 * @version  3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices handling.
 *
 * @class    WC_Free_Gift_Coupons_Admin_Notices
 * @version  2.0.0
 */
class WC_Free_Gift_Coupons_Admin_Notices {

	public static $meta_box_notices    = array();
	public static $admin_notices       = array();
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

		self::$maintenance_notices = get_option( 'wc_fgc_maintenance_notices', array() );

		// Show meta box notices.
		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
		// Save meta box notices.
		add_action( 'shutdown', array( __CLASS__, 'save_notices' ), 100 );
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
	 * @param  boolean  $save_notice
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
	 * Save errors to an option.
	 */
	public static function save_notices() {
		update_option( 'wc_fgc_meta_box_notices', self::$meta_box_notices );
		update_option( 'wc_fgc_maintenance_notices', self::$maintenance_notices );
	}

	/**
	 * Show any stored error messages.
	 */
	public static function output_notices() {

		$saved_notices = maybe_unserialize( get_option( 'wc_fgc_meta_box_notices', array() ) );
		$notices       = $saved_notices + self::$admin_notices;

		if ( ! empty( $notices ) ) {

			wp_enqueue_style( 'woocommerce_admin_styles' );

			foreach ( $notices as $notice ) {

				$dismiss_class = $notice[ 'dismiss_class' ] ? $notice[ 'dismiss_class' ] . ' is-persistent' : 'is-dismissible';

				echo '<div class="wc_fgc_notice woocommerce-message notice-' . esc_attr( $notice[ 'type' ] ) . ' notice ' . esc_attr( $dismiss_class ) . '">';

				if ( $notice[ 'dismiss_class' ] ) {
					printf( '<a class="wc-fgc-dismiss-notice woocommerce-message-close notice-dismiss" href="%s">%s</a>',
						esc_url( wp_nonce_url( add_query_arg( 'dismiss_wc_fgc_notice', $notice[ 'dismiss_class' ] ), 'wc_fgc_dismiss_notice_nonce', '_wc_fgc_admin_nonce' ) ),
						esc_html__( 'Dismiss', 'wc_free_gift_coupons' )
					);
				}

				echo '<p>' . wp_kses_post( $notice[ 'content' ] ) . '</p>';
				echo '</div>';
			}

			delete_option( 'wc_fgc_meta_box_notices' );
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
			if ( in_array( $type, self::$maintenance_notices, true ) ) {
				call_user_func( array( __CLASS__, $callback ) );
			}
		}
	}

	/**
	 * Add a maintenance notice to be displayed.
	 */
	public static function add_maintenance_notice( $notice_name ) {
		self::$maintenance_notices = array_unique( array_merge( self::$maintenance_notices, array( $notice_name ) ) );
	}

	/**
	 * Remove a maintenance notice.
	 */
	public static function remove_maintenance_notice( $notice_name ) {
		self::$maintenance_notices = array_diff( self::$maintenance_notices, array( $notice_name ) );
	}

	/**
	 * Add 'update' maintenance notice.
	 */
	public static function update_notice() {

		if ( ! class_exists( 'WC_Free_Gift_Coupons_Install' ) ) {
			return;
		}

		if ( WC_Free_Gift_Coupons_Install::is_update_pending() ) {

			$status = '';

			// Show notice to indicate that an update is in progress.
			if ( WC_Free_Gift_Coupons_Install::is_update_process_running() || WC_Free_Gift_Coupons_Install::is_update_queued() ) {

				$status = __( 'Your database is being updated in the background.', 'wc_free_gift_coupons' );

				// Check if the update process is running.
				if ( false === WC_Free_Gift_Coupons_Install::is_update_process_running() ) {
					$status .= self::get_force_update_prompt();
				}

				// Show a prompt to update.
			} elseif ( false === WC_Free_Gift_Coupons_Install::auto_update_enabled() && false === WC_Free_Gift_Coupons_Install::is_update_incomplete() ) {

				$status  = __( 'Your database needs to be updated to the latest version.', 'wc_free_gift_coupons' );
				$status .= self::get_trigger_update_prompt();

			} elseif ( WC_Free_Gift_Coupons_Install::is_update_incomplete() ) {

				$status  = __( 'Database update incomplete.', 'wc_free_gift_coupons' );
				$status .= self::get_failed_update_prompt();
			}

			if ( $status ) {
				$notice = '<strong>' . __( 'WooCommerce Free Gift Coupons Data Update', 'wc_free_gift_coupons' ) . '</strong> &#8211; ' . $status;
				self::add_notice( $notice, 'info' );
			}

			// Show persistent notice to indicate that the update process is complete.
		} else {
			$notice = __( 'WooCommerce Free Gift Coupons data update complete.', 'wc_free_gift_coupons' );
			self::add_notice( $notice, array( 'type' => 'info', 'dismiss_class' => 'update' ) );
		}
	}

	/**
	 * Returns a "trigger update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_trigger_update_prompt() {
		$update_url    = esc_url( wp_nonce_url( add_query_arg( 'trigger_wc_fgc_db_update', true, admin_url() ), 'wc_fgc_trigger_db_update_nonce', '_wc_fgc_admin_nonce' ) );
		$update_prompt = '<p><a href="' . $update_url . '" class="wc-fgc-update-now button-primary">' . __( 'Run the updater', 'wc_free_gift_coupons' ) . '</a></p>';
		return $update_prompt;
	}

	/**
	 * Returns a "force update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_force_update_prompt() {

		$fallback_prompt = '';
		$update_runtime  = get_option( 'wc_fgc_update_init', 0 );

		// Wait for at least 5 seconds.
		if ( gmdate( 'U' ) - $update_runtime > 5 ) {
			// Perhaps the upgrade process failed to start?
			$fallback_url  = esc_url( wp_nonce_url( add_query_arg( 'force_wc_fgc_db_update', true, admin_url() ), 'wc_fgc_force_db_update_nonce', '_wc_fgc_admin_nonce' ) );
			$fallback_link = '<a href="' . $fallback_url . '">' . __( 'run the update process manually', 'wc_free_gift_coupons' ) . '</a>';

			/* translators: %s: Fallback link to force update */
			$fallback_prompt = '<br/><em>' . sprintf( __( '&hellip;Taking a while? You may need to %s.', 'wc_free_gift_coupons' ), $fallback_link ) . '</em>';
		}

		return $fallback_prompt;
	}

	/**
	 * Returns a "failed update" notice component.
	 *
	 * @since  5.5.0
	 *
	 * @return string
	 */
	private static function get_failed_update_prompt() {
		$support_url  = esc_url( WC_FGC_SUPPORT_URL );
		$support_link = '<a href="' . $support_url . '">' . __( 'get in touch with us', 'wc_free_gift_coupons' ) . '</a>';
		/* translators: %s: Link to support */
		$support_prompt = '<br/><em>' . sprintf( __( 'If this message persists, please restore your database from a backup, or %s.', 'wc_free_gift_coupons' ), $support_link ) . '</em>';
		return $support_prompt;
	}

	/**
	 * Act upon clicking on a 'dismiss notice' link.
	 */
	public static function dismiss_notice_handler() {
		if ( isset( $_GET[ 'dismiss_wc_fgc_notice' ] ) && isset( $_GET[ '_wc_fgc_admin_nonce' ] ) ) {

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $_GET[ '_wc_fgc_admin_nonce' ], 'wc_fgc_dismiss_notice_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wc_free_gift_coupons' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You do not have permission to dismiss this notice.', 'wc_free_gift_coupons' ) );
			}

			$notice = sanitize_text_field( $_GET[ 'dismiss_wc_fgc_notice' ] );
			self::remove_maintenance_notice( $notice );
		}
	}
}

WC_Free_Gift_Coupons_Admin_Notices::init();
