<?php
/**
 * Display notices in the admin.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin_Notices class.
 */
class WC_Store_Credit_Admin_Notices {

	/**
	 * Stores core notices.
	 *
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Stores dismissible notices.
	 *
	 * @var array
	 */
	private static $dismiss_notices = array();

	/**
	 * Init.
	 *
	 * @since 3.0.0
	 */
	public static function init() {
		if ( ! class_exists( 'WC_Admin_Notices' ) ) {
			include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/class-wc-admin-notices.php';
		}

		add_action( 'wp_loaded', array( __CLASS__, 'load_dismiss_notices' ), 20 );
		add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ), 20 );
	}

	/**
	 * Loads dismissible notices.
	 *
	 * @since 3.2.0
	 */
	public static function load_dismiss_notices() {
		$notices         = WC_Admin_Notices::get_notices();
		$dismiss_notices = array();

		foreach ( $notices as $notice ) {
			if ( 0 !== strpos( $notice, 'wc_store_credit_' ) ) {
				continue;
			}

			$notice_html = get_option( 'woocommerce_admin_notice_' . $notice );

			if ( ! $notice_html ) {
				$dismiss_notices[] = $notice;
			}
		}

		self::$dismiss_notices = $dismiss_notices;
	}

	/**
	 * Gets core notices.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Gets dismissible notices.
	 *
	 * @since 3.2.0
	 *
	 * @return array
	 */
	public static function get_dismiss_notices() {
		return self::$dismiss_notices;
	}

	/**
	 * Displays a notice.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Optional. Notice HTML.
	 */
	public static function add_notice( $name, $notice_html = '' ) {
		if ( ! empty( $notice_html ) ) {
			self::add_dismiss_notice( $name, $notice_html );
		} else {
			self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
		}
	}

	/**
	 * Displays a dismissible notice.
	 *
	 * @since 3.2.0
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Optional. Notice HTML.
	 */
	public static function add_dismiss_notice( $name, $notice_html = '' ) {
		if ( empty( $notice_html ) ) {
			self::$dismiss_notices = array_unique( array_merge( self::get_dismiss_notices(), array( $name ) ) );
		}

		WC_Admin_Notices::add_custom_notice( $name, $notice_html );
	}

	/**
	 * Removes a notice from being displayed.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Notice name.
	 */
	public static function remove_notice( $name ) {
		if ( in_array( $name, self::get_notices(), true ) ) {
			self::$notices = array_diff( self::get_notices(), array( $name ) );
		} else {
			self::$dismiss_notices = array_diff( self::get_dismiss_notices(), array( $name ) );
			WC_Admin_Notices::remove_notice( $name );
		}
	}

	/**
	 * Gets if a notice is being shown or not.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Notice name.
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		$has_notice = in_array( $name, self::get_notices(), true );

		if ( ! $has_notice ) {
			$has_notice = WC_Admin_Notices::has_notice( $name );
		}

		return $has_notice;
	}

	/**
	 * Gets if there are notices registered or not.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function has_notices() {
		$notices         = self::get_notices();
		$dismiss_notices = self::get_dismiss_notices();

		return ( ! empty( $notices ) || ! empty( $dismiss_notices ) );
	}

	/**
	 * Adds notices + styles if needed.
	 *
	 * @since 3.0.0
	 */
	public static function add_notices() {
		if ( ! self::has_notices() ) {
			return;
		}

		// If the notices scripts has already been enqueued by WC, we don't need to check these conditions.
		if ( ! wp_style_is( 'woocommerce-activation' ) ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$screen_id       = wc_store_credit_get_current_screen_id();
			$show_on_screens = array(
				'dashboard',
				'plugins',
			);

			// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
			if ( ! in_array( $screen_id, wc_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
				return;
			}

			self::enqueue_scripts();
		}

		add_action( 'admin_notices', array( __CLASS__, 'output_notices' ) );
	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 3.0.0
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'woocommerce-activation', plugins_url( '/assets/css/activation.css', WC_PLUGIN_FILE ), array(), WC_VERSION );
		wp_style_add_data( 'woocommerce-activation', 'rtl', 'replace' );
	}

	/**
	 * Outputs the notices.
	 *
	 * @since 3.0.0
	 */
	public static function output_notices() {
		// Display core notices first.
		$notices = array_merge( self::get_notices(), self::get_dismiss_notices() );

		foreach ( $notices as $notice ) {
			echo self::get_notice_content_from_file( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Gets the content for the notice from its own file.
	 *
	 * @since 3.2.0
	 *
	 * @param string $notice The notice name.
	 * @return string
	 */
	protected static function get_notice_content_from_file( $notice ) {
		$file = str_replace( array( 'wc_store_credit_', '_' ), array( '', '-' ), $notice );
		$path = __DIR__ . "/notices/{$file}.php";

		$content = '';

		if ( is_readable( $path ) ) {
			ob_start();
			include $path;
			$content = ob_get_clean();
		}

		return $content;
	}
}

WC_Store_Credit_Admin_Notices::init();
