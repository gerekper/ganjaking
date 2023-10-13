<?php
/**
 * Admin Notices
 *
 * @package  WooCommerce Mix and Match Products/Admin
 * @since    1.2.0
 * @version  2.5.0
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
	 * Stores notices.
	 *
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @var array
	 */
	private static $core_notices = array(
		'update' => 'update_notice',
	);

	/**
	 * Constructor.
	 */
	public static function init() {

		self::$notices = get_option( 'wc_mnm_admin_notices', array() );

		// Handle dimissing notices.
		add_action( 'admin_init', array( __CLASS__, 'hide_notices' ), 20 );

		// Save notices.
		add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );

		// Show maintenance notices.
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
		}
	}

	/**
	 * Store  notices to DB.
	 */
	public static function store_notices() {
		if ( ! empty( self::get_notices() ) ) {
			update_option( 'wc_mnm_admin_notices', self::get_notices() );
		} else {
			delete_option( 'wc_mnm_admin_notices' );
		}
	}

	/**
	 * Get notices
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$notices = array();
	}

	/**
	 * Add a notice/error.
	 *
	 * @param string $name Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 * @param  bool  $save_notice - deprecated 3rd arg.
	 */
	public static function add_notice( $name, $force_save = false, $save_notice = false ) {

		if ( is_array( $force_save ) ) {
			wc_deprecated_argument( '__FUNCTION__', '2.0.0', 'WC_MNM_Admin_Notices::add_notice() cannot be used to set a custom notice text. Use WC_MNM_Admin_Notices::add_custom_notice( $name, $notice_html ) instead.' );
			$html = ! empty( self::$core_notices[ $name ] ) ? $name : false;
			return WC_Admin_Notices::add_custom_notice( sanitize_text_field( $name ), $html );
		}

		self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::store_notices();
		}
	}

	/**
	 * Remove a notice from being displayed.
	 *
	 * @since 2.0.0
	 *
	 * @see WC_Admin_Notices::hide_notice() if there is a need to make a notice dismissable and store user meta. More relevant to optional notices.
	 *
	 * @param string $name Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 */
	public static function remove_notice( $name, $force_save = false ) {
		self::$notices = array_diff( self::get_notices(), array( $name ) );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::store_notices();
		}

		// Needed to dismiss Note notices.
		do_action( 'wc_mnm_hide_' . $name . '_notice' );
	}

	/**
	 * See if a notice is being shown.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name Notice name.
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 *
	 * @since 2.0.0
	 */
	public static function hide_notices() {

		if ( isset( $_GET['wc-mnm-hide-notice'] ) && isset( $_GET['_wc_mnm_notice_nonce'] ) ) {

			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wc_mnm_notice_nonce'] ) ), 'wc_mnm_hide_notices' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce-mix-and-match-products' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You do not have permission to dismiss this notice.', 'woocommerce-mix-and-match-products' ) );
			}

			$notice = sanitize_text_field( wp_unslash( $_GET['wc-mnm-hide-notice'] ) );

			// If we dismiss an update notice that has a cleanup prompt, let's cleanup the transient.
			if ( 'cleanup' === $notice ) {
				delete_transient( 'wc_mnm_show_2x00_cleanup_legacy_child_meta' );
				$notice = 'update';
			}

			self::remove_notice( $notice );
		}
	}

	/**
	 * Show maintenance notices.
	 */
	public static function add_notices() {

		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, wc_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		// General admin notice styles.
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$style_path = 'assets/css/admin/mnm-admin' . $suffix . '.css';

		wp_enqueue_style( 'wc-mnm-admin', WC_Mix_and_Match()->plugin_url() . '/' . $style_path, array(), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $style_path ) );
		wp_style_add_data( 'wc-mnm-admin', 'rtl', 'replace' );
		if ( $suffix ) {
			wp_style_add_data( 'wc-mnm-admin', 'suffix', '.min' );
		}

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
			}
		}
	}

	/**
	 * Add custom notice.
	 * NB: see WC_Admin_Notices:add_custom_notice if we need to make these persistent/dismissiable.
	 *
	 * @since 2.0.0
	 *
	 * @param string $name        Notice name - currently not used.
	 * @param string $notice_html Notice HTML.
	 * @param string $notice_type Class added to notice div.
	 */
	public static function add_custom_notice( $name = '', $notice_html = '', $notice_type = 'error' ) {

		add_action(
			'admin_notices',
			function () use ( $notice_html, $notice_type ) {

				if ( $notice_html ) {
					include __DIR__ . '/views/html-notice-custom.php';
				}
			}
		);
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

		if ( WC_MNM_Install::needs_db_update() ) {
			$next_scheduled_date = WC()->queue()->get_next( 'wc_mnm_run_update_callback', null, 'wc_mnm_db_updates' );

			if ( $next_scheduled_date || ! empty( $_GET['do_update_wc_mnm'] ) ) { // WPCS: input var ok, CSRF ok.
				include __DIR__ . '/views/html-notice-updating.php';
			} elseif ( ! WC_MNM_Install::auto_update_enabled() ) {

				if ( version_compare( '2.0.0', WC_MNM_Install::get_latest_update_version(), '==' ) ) {
					include __DIR__ . '/views/html-notice-update-2x00.php';
				} else {
					include __DIR__ . '/views/html-notice-update.php';
				}
			}
		} else {
			include __DIR__ . '/views/html-notice-updated.php';
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Show any stored error messages.
	 *
	 * @deprecated 2.0.0
	 */
	public static function output_notices() {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::output_notices()', '2.0.0', 'Method renamed add_notices().' );
		return self::add_notices();
	}

	/**
	 * Add a maintenance notice to be displayed.
	 *
	 * @deprecated 2.0.0
	 *
	 * @param  string  $notice_name
	 */
	public static function add_maintenance_notice( $notice_name ) {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::add_maintenance_notice()', '2.0.0', 'Maintenance notices no longer exist. Use WC_MNM_Admin_Notices::add_notice().' );
		return self::add_notice( $notice_name, true );
	}

	/**
	 * Remove a maintenance notice.
	 *
	 * @deprecated 2.0.0
	 *
	 * @param  string  $notice_name
	 */
	public static function remove_maintenance_notice( $notice_name ) {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::remove_maintenance_notice()', '2.0.0', 'Maintenance notices no longer exist. Use WC_MNM_Admin_Notices::hide_notice().' );
		return self::hide_notice( $notice );
	}


	/**
	 * Dismisses a notice.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @param  string  $notice
	 */
	public static function dismiss_notice( $notice ) {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::is_dismissible_notice_dismissed()', '2.0.0', 'Method renamed hide_notice().' );
		return self::hide_notice( $notice );
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
		wc_deprecated_function( 'WC_MNM_Admin_Notices::is_dismissible_notice_dismissed()', '2.0.0', 'Method has no replacement.' );
		return in_array( $notice_name, self::$maintenance_notices );
	}

	/**
	 * Checks if a dismissible notice has been dismissed in the past.
	 *
	 * @since  1.10.0
	 * @deprecated 2.0.0
	 *
	 * @param  string  $notice_name
	 * @return boolean
	 */
	public static function is_dismissible_notice_dismissed( $notice_name ) {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::is_dismissible_notice_dismissed()', '2.0.0', 'Method has no replacement.' );
		return false;
	}


	/**
	 * Save errors to an option.
	 *
	 * @deprecated 2.0.0
	 */
	public static function save_notices() {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::save_notices()', '2.0.0', 'Method renamed store_notices().' );
		return self::store_notices();
	}

	/**
	 * Show maintenance notices.
	 */
	public static function hook_maintenance_notices() {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::hook_maintenance_notices()', '2.0.0', 'Method renamed add_notices().' );
		return self::add_notices();
	}

	/**
	 * Act upon clicking on a 'dismiss notice' link.
	 *
	 * @deprecated 2.0.0
	 */
	public static function dismiss_notice_handler() {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::dismiss_notice_handler()', '2.0.0', 'Method renamed hide_notices().' );
		return self::hide_notices();
	}

	/**
	 * Add 'updating' maintenance notice.
	 *
	 * @deprecated 1.10.0
	 */
	public static function updating_notice() {
		wc_deprecated_function( 'WC_MNM_Admin_Notices::updating_notice()', '1.1.0', 'Method renamed update_notice().' );
		return self::update_notice();
	}
}

WC_MNM_Admin_Notices::init();
